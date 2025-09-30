<?php

namespace Modules\ProToolsKit\Http\Controllers;

use App\Business;
use App\Charts\CommonChart;
use App\System;
use Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Superadmin\Entities\Subscription;
use Illuminate\Routing\Controller;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


use App\Utils\BusinessUtil;

use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use Modules\Crm\Entities\CrmContact;
use Modules\Crm\Entities\Campaign;


/******* custom_code 09062024  FLEXPAY_PAIMENT */
//pour recuper le location ou les paiements saas se font
use App\BusinessLocation;
//log paiement mobile money
use App\MobilemoneyPayLine;

//pour generer une facture automatiquement
use App\Contact;
use App\Product;
use App\Unit;
use App\User;




/********************** */


class SMSController extends Controller
{
    protected $notificationUtil;

    protected $moduleUtil;

    public function __construct(NotificationUtil $notificationUtil, ModuleUtil $moduleUtil)
    {
        
        //si les données de l'api des sms ne sont pas encore assigné dans la session
        $this->middleware(function ($request, $next) {
            if (empty(request()->session()->get('sms.api_key'))) {
                //les details du compte  
                $business_id = request()->session()->get('user.business_id');

                $headers = [
                    'Api-key' => env('SMS_ADMIN_API_KEY'),
                    'Content-Type' => 'application/json',
                ];
                $postdata = ["username" => "USER_H360_" . $business_id];
                $client = new \GuzzleHttp\Client([
                    \GuzzleHttp\RequestOptions::VERIFY => false
                ]);
                $_response = $client->get(env('SMS_ADMIN_API_URL') . '/users', [
                    'headers' => $headers,
                    'body' => json_encode($postdata)
                ]);
                $_sms_users = json_decode($_response->getBody()->getContents());
                $sms_users = collect($_sms_users); //convertir en collection afin de faire les recherche dedans

                if (empty($sms_users)) {
                    dd("impossible d'utiliser les sms car vous n'avez pas des representant");
                }
                request()->session()->put('sms', $sms_users[0]);
            }

            return $next($request);
        });
        
        $this->notificationUtil = $notificationUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Returns the list of all configured payment gateway
     *
     * @return Response
     */
    public function _payment_gateways()
    {
        $gateways = [];
        //-- personnalize custom code 03052024-MOBILEMONEYADMINPAY -- 03052024 --
        if (env('FLEXPAY_MERCHANT') && env('FLEXPAY_TOKEN')) {
            $gateways['flexpay'] = 'flexpay';
        }
        return $gateways;
        /************************************* */



        //Check if stripe is configured or not
        if (env('STRIPE_PUB_KEY') && env('STRIPE_SECRET_KEY')) {
            $gateways['stripe'] = 'Stripe';
        }

        //Check if paypal is configured or not
        if (env('PAYPAL_CLIENT_ID') && env('PAYPAL_APP_SECRET')) {
            $gateways['paypal'] = 'PayPal';
        }

        //Check if Razorpay is configured or not
        if ((env('RAZORPAY_KEY_ID') && env('RAZORPAY_KEY_SECRET'))) {
            $gateways['razorpay'] = 'Razor Pay';
        }

        //Check if Pesapal is configured or not
        if ((config('pesapal.consumer_key') && config('pesapal.consumer_secret'))) {
            $gateways['pesapal'] = 'PesaPal';
        }

        //check if Paystack is configured or not
        $system = System::getCurrency();
        if (in_array($system->country, ['Nigeria', 'Ghana']) && (config('paystack.publicKey') && config('paystack.secretKey'))) {
            $gateways['paystack'] = 'Paystack';
        }

        //check if Flutterwave is configured or not
        if (env('FLUTTERWAVE_PUBLIC_KEY') && env('FLUTTERWAVE_SECRET_KEY') && env('FLUTTERWAVE_ENCRYPTION_KEY')) {
            $gateways['flutterwave'] = 'Flutterwave';
        }

        //-- personnalize custom code 03052024-MOBILEMONEYADMINPAY -- 03052024 --
        if (env('FLEXPAY_MERCHANT') && env('FLEXPAY_TOKEN')) {
            $gateways['flexpay'] = 'flexpay';
        }
        /********************END  */

        // check if offline payment is enabled or not
        $is_offline_payment_enabled = System::getProperty('enable_offline_payment');

        if ($is_offline_payment_enabled) {
            $gateways['offline'] = 'Offline';
        }

        return $gateways;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
                
        //dd(request()->session()->get('sms')->api_key);
        //les details du compte  
        $business_id = request()->session()->get('user.business_id');
        //dd(request()->session()->get('sms'));

        $headers = [
            'Api-key' => request()->session()->get('sms')->api_key,
            'Content-Type' => 'application/json',
        ];
        $postdata = [""];
        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        $_response = $client->get(env('SMS_ADMIN_API_URL') . '/userdashboard', [
            'headers' => $headers,
            'body' => json_encode($postdata)
        ]);
        $_userdashboard = json_decode($_response->getBody()->getContents());
        $userdashboard = collect($_userdashboard); //convertir en collection afin de faire les recherche dedans


        /********pour recuperer les contacts de l'entreprise */
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_campaigns = auth()->user()->can('crm.access_all_campaigns');
        $can_access_own_campaigns = auth()->user()->can('crm.access_own_campaigns');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_campaigns || $can_access_own_campaigns)) {
            abort(403, 'Unauthorized action.');
        }

        $tags = Campaign::getTags();
        $leads = CrmContact::leadsDropdown($business_id, false);
        $customers = CrmContact::customersDropdown($business_id, false);
        $contact_ids = $request->get('contact_ids', '');

        $contacts = [];
        foreach ($leads as $key => $lead) {
            $contacts[$key] = $lead;
        }

        foreach ($customers as $key => $customer) {
            $contacts[$key] = $customer;
        }

        return view('protoolskit::sms.index')
            ->with(compact('userdashboard', 'tags', 'leads', 'customers', 'contact_ids', 'contacts'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function history()
    {
        if (request()->ajax()) {
            //dd(request()->session()->get('sms')->api_key);
            //les details du compte  
            $business_id = request()->session()->get('user.business_id');
            //dd(request()->session()->get('sms'));

            $headers = [
                'Api-key' => request()->session()->get('sms')->api_key,
                'Content-Type' => 'application/json',
            ];
            $postdata = [""];
            $client = new \GuzzleHttp\Client([
                \GuzzleHttp\RequestOptions::VERIFY => false
            ]);
            $_response = $client->get(env('SMS_ADMIN_API_URL') . '/usersmshistory', [
                'headers' => $headers,
                'body' => json_encode($postdata)
            ]);
            $_result = json_decode($_response->getBody()->getContents());
            $data = collect($_result->smslogs); //convertir en collection afin de faire les recherche dedans
            return Datatables::of($data)
                ->addColumn('credit', function ($row) {
                    return ceil($row->word_length / 160);
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return "**Attente";
                    } elseif ($row->status == 1) {
                        return "**Planifié";
                    } elseif ($row->status == 1) {
                        return "**Echoué";
                    } else {
                        return "**Envoyé";
                    }
                })
                ->make(true);
        }

        return view('protoolskit::sms.history');
    }


    /**
     * express send message
     *
     * @return Response
     */
    public function showSendSms(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $can_access_all_campaigns = auth()->user()->can('crm.access_all_campaigns');
        $can_access_own_campaigns = auth()->user()->can('crm.access_own_campaigns');

        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'crm_module')) || !($can_access_all_campaigns || $can_access_own_campaigns)) {
            abort(403, 'Unauthorized action.');
        }

        $tags = Campaign::getTags();
        $leads = CrmContact::leadsDropdown($business_id, false);
        $customers = CrmContact::customersDropdown($business_id, false);
        $contact_ids = $request->get('contact_ids', '');

        $contacts = [];
        foreach ($leads as $key => $lead) {
            $contacts[$key] = $lead;
        }

        foreach ($customers as $key => $customer) {
            $contacts[$key] = $customer;
        }

        return view('protoolskit::sms.show_send')
            ->with(compact('tags', 'leads', 'customers', 'contact_ids', 'contacts'));

        return view('protoolskit::sms.show_send');
    }

    /**
     * express send message
     *
     * @return Response
     */
    public function updateSmsSenderId(Request $request)
    {
        if (empty($request->has('sender_id'))) {
            return response()->json([
                'status' => 1,
                'message' => 'le sender_id ne peut etre vide'
            ]);
        }

        $headers = [
            'Api-key' => request()->session()->get('sms')->api_key,
            'Content-Type' => 'application/json',
        ];
        $postdata = ["sender_id" => $request->input('sender_id'), "user_id" => request()->session()->get('sms')->id];
        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        $_response = $client->put(env('SMS_ADMIN_API_URL') . '/savegsatewaycredential', [
            'headers' => $headers,
            'body' => json_encode($postdata)
        ]);
        $response = json_decode($_response->getBody()->getContents());
        //dd($response);
        if (empty($response->status)) {
            //je suprime la session pour que ca se mette a jour dans le constructeur automatiquement
            request()->session()->put('sms', null);
        }
        $output = [
            'success' => empty($response->status),
            'msg' => $response->message,
        ];
        return redirect()->action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'index'])->with('status', $output);
    }



    /**
     * manualSendSms
     *
     * @return Response
     */
    public function manualSendSms(Request $request)
    {
        $output = [];
        //dd($request);
        $headers = [
            'Api-key' => request()->session()->get('sms')->api_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        $headers_for_curl = [
            'Api-key: '.request()->session()->get('sms')->api_key,
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        $manual_to = explode(',', $request->input('manual_to'));
        if (empty($manual_to)) {
            $output = [
                'success' => 0,
                'msg' => "Aucun correspondant trouvé",
            ];
        }
        $sms_body = $request->input('sms_body');
        if (empty($manual_to)) {
            $output = [
                'success' => 0,
                'msg' => "SMS ne peut etre vide",
            ];
        }

        if (!empty($output)) {
            return redirect()->action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'index'])->with('status', $output);
        }

        $contact = [];
        foreach ($manual_to as $key => $value) {
            $contact[] = ['number' => $value, "sms_type" => "plain", "body" => $sms_body];
        }
        $postdata = ["contact" => $contact];

        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        try {
            $curl = curl_init();
            //dd($headers_for_curl);

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('SMS_ADMIN_API_SMS') . "/send",
                //CURLOPT_URL => 'http://localhost/api/sms/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postdata),
                CURLOPT_HTTPHEADER => array(
        'Api-key: '.request()->session()->get('sms')->api_key,
        //'Api-key: 9c53e514-76f6-4ffe-b7f5-5ad76e63cfc3	',//local
        'Content-Type: application/json',
    ),
            ));

            $response = json_decode(curl_exec($curl));
            curl_close($curl);
            //dd($response);
            //var_dump($_response->getBody());
            //dd($_response->getBody()->getContents());
            //dd($response );
            if ($response->status == 'success') {
                //je suprime la session pour que ca se mette a jour dans le constructeur automatiquement
                $output = [
                    'success' => 1,
                    'msg' => $response->message,
                ];
            } elseif (empty($response->status)) {
                $output = [
                    'success' => 1,
                    'msg' => "**Improblem inconnu est survenu lors de l'envoi d'SMS veuillez verifier vos logs",
                ];
            }
        } catch (\Throwable $e) {
            //dd($e->getResponse()->getBody()->getContents());
            try {
                $response = json_decode($e->getResponse()->getBody()->getContents());
                $output = [
                    'success' => 0,
                    'msg' => $response->message,
                ];
            } catch (\Throwable $th) {
                $output = [
                    'success' => 0,
                    'msg' => "Error",
                ];
            }
        }
    
        //verification si le message vient de auto_sms
        if(!empty($request->input('auto_sms'))){
            return ($output['success']==1)? true : false;
        }
        return redirect()->action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'index'])->with('status', $output);
        
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function buycredit(Request $request)
    {
        //dd(request()->session()->get('sms')->api_key);
        //les details du compte  
        $business_id = request()->session()->get('user.business_id');
        //dd(request()->session()->get('sms'));

        $headers = [
            'Api-key' => request()->session()->get('sms')->api_key,
            'Content-Type' => 'application/json',
        ];
        $postdata = [""];
        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        $_response = $client->get(env('SMS_ADMIN_API_URL') . '/userbuyplan', [
            'headers' => $headers,
            'body' => json_encode($postdata)
        ]);

        $_result = json_decode($_response->getBody()->getContents());
        //dd($_result);
        $data = collect($_result); //convertir en collection afin de faire les recherche dedans

        //dd($data['plans']);


        //je verifie si le plan est reconnu dans notre system
        $plan_id = $request->input('plan_id');
        if (!empty($plan_id)) {
            $output = [];
            if (!collect($data['plans'])->contains('id', $plan_id)) {
                $output['success'] = 0;
                $output['msg'] = "Plan SMS nos reconnu dans notre système";
            }
            if (!empty($output)) {
                return redirect()->action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'buycredit'])->with('status', $output);
            }
        } else {
            return view('protoolskit::sms.buycredit')->with(compact('data'));
        }

        //dd(1111);

        /***********PROCEDURE DE PAIEMENT***********/
        //1) TRIGGER MOBILE MONEY PAY
        //2) CREATION DE LA FACTURE
        //3) ENREGISTREMENT DU PLAN SMS
        /*
        $request->request->add(['payment_ref' => 'paymbongo-123456']);
        $request->request->add(['invoice_url' => 'google.com']);
        */

        /***************************** */
        //dd(request()->session()->get('sms')->api_key);
        //les details du compte  
        $business_id = request()->session()->get('user.business_id');
        //dd(request()->session()->get('sms'));

        $headers = [
            'Api-key' => request()->session()->get('sms')->api_key,
            'Content-Type' => 'application/json',
        ];
        $postdata = [
            'plan_id' => $plan_id,
            'payment_ref' => 'paymbongo-123456',
            'invoice_url' => 'google.com',
        ];
        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        $_response = $client->get(env('SMS_ADMIN_API_URL') . '/userbuyplan', [
            'headers' => $headers,
            'body' => json_encode($postdata)
        ]);
        //dd($_response);

        $_result = json_decode($_response->getBody()->getContents());
        dd($_result);
        $data = collect($_result->smslogs); //convertir en collection afin de faire les recherche dedans



        if (request()->ajax()) {
            //dd(request()->session()->get('sms')->api_key);
            //les details du compte  
            $business_id = request()->session()->get('user.business_id');
            //dd(request()->session()->get('sms'));

            $headers = [
                'Api-key' => request()->session()->get('sms')->api_key,
                'Content-Type' => 'application/json',
            ];
            $postdata = [
                'plan_id' => $plan_id,
                'payment_ref' => 'paymbongo-123456',
                'invoice_url' => 'google.com',
            ];
            $client = new \GuzzleHttp\Client([
                \GuzzleHttp\RequestOptions::VERIFY => false
            ]);
            $_response = $client->get(env('SMS_ADMIN_API_URL') . '/userbuyplan', [
                'headers' => $headers,
                'body' => json_encode($postdata)
            ]);
            dd($_response);

            $_result = json_decode($_response->getBody()->getContents());
            $data = collect($_result->smslogs); //convertir en collection afin de faire les recherche dedans




            return Datatables::of($data)
                ->addColumn('credit', function ($row) {
                    return ceil($row->word_length / 160);
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return "**Attente";
                    } elseif ($row->status == 1) {
                        return "**Planifié";
                    } elseif ($row->status == 1) {
                        return "**Echoué";
                    } else {
                        return "**Envoyé";
                    }
                })
                ->make(true);
        }

        return view('protoolskit::sms.history');
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function paycredit(Request $request, $plan_id, $form_register = null)
    {
        //dd($plan_id);
        $business_id = request()->session()->get('user.business_id');
        //dd(request()->session()->get('sms'));

        $headers = [
            'Api-key' => request()->session()->get('sms')->api_key,
            'Content-Type' => 'application/json',
        ];
        $postdata = [""];
        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        $_response = $client->get(env('SMS_ADMIN_API_URL') . '/userbuyplan', [
            'headers' => $headers,
            'body' => json_encode($postdata)
        ]);

        $_result = json_decode($_response->getBody()->getContents());
        //dd($_result);
        $data = collect($_result); //convertir en collection afin de faire les recherche dedans

        //dd($data['plans']);


        //je verifie si le plan est reconnu dans notre system
        //$plan_id=$request->input('id');

        $plan = collect($data['plans'])->first(function ($item) use ($plan_id) {
            return $item->id == $plan_id;
        });

        if (empty($plan)) {
            $output['success'] = 0;
            $output['msg'] = "Plan SMS nos reconnu dans notre système";
            return redirect()->action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'buycredit'])->with('status', $output);
        }

        if (empty($form_register)) {
            $layout = 'layouts.app';
        } else {
            $layout = 'layouts.auth';
        }
        $gateways = $this->_payment_gateways();

        return view('protoolskit::sms.paycredit')
            ->with(compact('plan', 'layout', 'gateways'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function trigger_flexpay(Request $request, $plan_id)
    {
        //je retrouve le plan_par son id
        $business_id = request()->session()->get('user.business_id');
        //dd(request()->session()->get('sms'));

        $headers = [
            'Api-key' => request()->session()->get('sms')->api_key,
            'Content-Type' => 'application/json',
        ];
        $postdata = [""];
        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        $_response = $client->get(env('SMS_ADMIN_API_URL') . '/userbuyplan', [
            'headers' => $headers,
            'body' => json_encode($postdata)
        ]);

        $_result = json_decode($_response->getBody()->getContents());
        //dd($_result);
        $data = collect($_result); //convertir en collection afin de faire les recherche dedans

        //dd($data['plans']);


        //je verifie si le plan est reconnu dans notre system
        //$plan_id=$request->input('id');

        $plan = collect($data['plans'])->first(function ($item) use ($plan_id) {
            return $item->id == $plan_id;
        });

        if (empty($plan)) {
            $output['success'] = 0;
            $output['msg'] = "Plan SMS nos reconnu dans notre système";
            return redirect()->action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'buycredit'])->with('status', $output);
        }

        /******* custom_code 09062024  FLEXPAY_PAIMENT */
        //on gerere flexpay en verifiant d'abord que le client a choisi de payer via flexpay
        //dd($request->phone);
        //dd($this->_payment_gateways());

        if (!(isset($this->_payment_gateways()['flexpay']) && !empty($request->phone))) {
            //return 7410;
            $output = [
                'success' => 0,
                'msg' => '**error, veuillez réessayer svp!!',
            ];
            return back()->with('status', $output);
            return $this->pay_flexpay($business_id, $business_name, $package, $request);
        }

        //je verifie d'abor si l'entreprise a un contact lié afin que la facture soit generée en son nom
        // sinon on lui dit d'informer l'administrateur
        $contact = Contact::where('business_id', env('ADMINISTRATOR_BUSINESS'))
            ->where(env('ADMINISTRATOR_CONTACT_CUSTOMLABEL'), $business_id)->first();
        if (empty($contact)) {

            $output = ['success' => 0, 'msg' => "**Entreprise pas encore representée , veuillez contacter les administrateurs de H360 pour vous créer un representant"];

            return \Response::json($output, 200);
            //return $output;
            return \Response::json($output, 200);
            //dd(7441);
        }
        /************************************** */


        $fexpay_merchant = env('FLEXPAY_MERCHANT');
        $fexpay_token = env('FLEXPAY_TOKEN');

        $user_id = request()->session()->get('user.id');

        $admin_business = Business::findorfail(env('ADMINISTRATOR_BUSINESS'));
        $admin_businesslocation_saas = BusinessLocation::where('business_id', env('ADMINISTRATOR_BUSINESS'))->find(env('ADMINISTRATOR_BUSINESSLOCATION_SAAS'));
        $admin_business_details = Business::leftjoin('tax_rates AS TR', 'business.default_sales_tax', 'TR.id')
            ->leftjoin('currencies AS cur', 'business.currency_id', 'cur.id')
            ->select(
                'business.*',
                'cur.id as currency_id',
                'cur.code as currency_code',
                'cur.symbol as currency_symbol',
                'thousand_separator',
                'decimal_separator',
                'TR.amount AS tax_calculation_amount',
                'business.default_sales_discount'
            )
            ->where('business.id', env('ADMINISTRATOR_BUSINESS'))
            ->first();

        $country = "243";
        $phone = trim($request->phone);
        $currency_code = $admin_business_details->currency_code;
        $currency_id = $admin_business_details->currency_id;


        $country = "243";
        $phone = trim($request->phone);
        $currency_code = $admin_business_details->currency_code;
        $currency_id = $admin_business_details->currency_id;
        //dd($currency_id);
        if (strlen($phone) != 9 && strlen($phone) != 10 && strlen($phone) != 12 && strlen($phone) != 13) {
            //erreur
            $output = [
                'success' => 0,
                'msg' => 'desolé, Le numéro mobile money non pris en charge'
            ];
            return \Response::json($output, 200);
        } else {
            $phone = $country . substr($request->phone, -9);
        }

        $total_payable = (float) $plan->amount * env('ADMINISTRATOR_SUBSCRIPTION_RATE_CONVERSION');



        $payment_ref = 'smscredit-' . $plan->id . "-" . $business_id . "-" . $user_id;



        $url = '#';
        $charge = [
            'amount' => $total_payable,
            "phone" => $phone,
            "reference" => $payment_ref,

            "merchant" => $fexpay_merchant,
            "type" => "1",
            'currency' => strtolower($currency_code),
            "callbackUrl" => $url,
            "approve_url" => $url,
            "cancel_url" => $url,
            "decline_url" => $url,
        ];
        //dd($charge);
        $data = json_encode($charge);
        $gateway = "https://backend.flexpay.cd/api/rest/v1/paymentService";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $gateway);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $fexpay_token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            $output = [
                'success' => 0,
                'msg' => 'Une erreur lors du traitement de votre requête'
            ];
            return \Response::json($output, 200);
            //return back()->with('status', $output);
        } else {
            curl_close($ch);
            $jsonRes = json_decode($response);
            $code = $jsonRes->code;

            //return response()->json(['message' => 'Not Found!'], 404);
            if ($code != "0") {
                $output = [
                    'success' => 0,
                    'msg' => ($jsonRes->message && !empty($jsonRes->message)) ? $jsonRes->message : 'Impossible de traiter la demande, veuillez réessayer'
                ];
                return \Response::json($output, 200);
            } else {

                //{{--  personnalize custom_code 25042024-MOBILEMONEY -- 25042024}}
                $status = '';

                switch ($code) {
                    case 0:
                        $status = 'pending';
                        break;

                    default:
                        # code...
                        break;
                }


                $payment_ref = '';
                $payment_ref = 'smscredit-' . $plan->id . "-" . $business_id . "-" . $user_id;
                $data = [
                    'business_id' => $business_id,
                    'currency_id' => $currency_id,
                    'package_id' => null,
                    'order_number' => $jsonRes->orderNumber,
                    //'payment_ref' => $payment_ref."-".$jsonRes->orderNumber,
                    'payment_ref' => $payment_ref . "-" . $jsonRes->orderNumber,
                    'amount' => $total_payable,
                    'method' => $request->gateway, //flexpay
                    'status' => $status,
                    'mobile' => $phone,
                    'additional_notes' => $jsonRes->message,
                ];

                $mmp = MobilemoneyPayLine::create($data);
                //----------------------- END PERSONNALIZE custom_code-----------------------------------//////

                $message = $jsonRes->message;
                $orderNumber = $jsonRes->orderNumber;

                $data = file_get_contents('php://input');
                $json = json_decode($data, true);

                $output = [
                    'success' => 1,
                    'msg' => 'Veuillez confirmer le paiement en repondant au Push Notification'
                ];

                if ($request->ajax()) {
                    return [
                        'data' => $jsonRes,
                        'sell' => str_replace("/home", "/" . $url, route('home')),
                    ];

                    return $json;
                }
            }
        }

        dd($response);



        dd($plan);
    }



    /**
     * Une fois le paiement confirmé on generer une facture au niveau du compte entreprise de lentreprise h360 , dans le location "h360 saas"
     * when payment gateway is Flexpay payment gateway request package_id
     * is transaction_id & merchant_reference in session contains
     * the package_id.
     *
     */
    protected function addSellPos($mmpl, $coupon_code, $note = null)
    {




        /*******************custom_code 12062024-AUTOINVOICE *************** */
        $admin_business = Business::findorfail(env('ADMINISTRATOR_BUSINESS'));
        $admin_businesslocation_saas_id = env('ADMINISTRATOR_BUSINESS');

        ///nous allons faire passer ces donner pour que la requete ne dependent pas des parametre de session car par moment 
        //quand on change les parametres de sessions , 
        //l'utilisateur se voit etre connecté dans le business entité de l'entreprise,ce qui peut trainer un grand probleme de securité
        $csrf_token = csrf_token();
        $user_business_id = env('ADMINISTRATOR_BUSINESS');
        $user_id = env('ADMINISTRATOR_SUBSCRIPTION_USERAGENT');
        $business_sales_cmsn_agnt = "00";
        $business_enable_rp = $admin_business->enable_rp;
        $business_accounting_method = $admin_business->accounting_method;
        /*
        echo date('h:i:s') . "<br>";

//sleep for 3 seconds
sleep(5);

//start again
echo date('h:i:s');
        dd($csrf_token."<---->".csrf_token());
        ///////////////////////////////
        dd($business_accounting_method);
        */

        //generer la facture automatiquement
        //$contact = Contact::where('business_id', env('ADMINISTRATOR_BUSINESS'))->find($mmpl->business_id);
        //$contacts = Contact::where('business_id', env('ADMINISTRATOR_BUSINESS'))->find(27);
        $contact = Contact::where('business_id', env('ADMINISTRATOR_BUSINESS'))
            ->where(env('ADMINISTRATOR_CONTACT_CUSTOMLABEL'), $mmpl->business_id)->first();
        /*
            $contact_id=env('ADMINISTRATOR_CONTACT_DEFAULT_ID');
        if(!empty($contact)){
            //$contact_id= $contact->id;
        }
        
        dd($contact_id);
        */





        $explode_payment_ref = explode('-', $mmpl->payment_ref); //on explode payment_ref pour recuperer package_id(1) business_id(2) user_id(3)  et le code coupn(4) , et orderNumber(5) si il y en a
        //on recuper le business id qui fait l'object d paiement
        $business_id = 0;
        if (isset($explode_payment_ref[2])) {
            $business_id = $explode_payment_ref[2];
        }




        //on retrouve le package qui fait l'objet de paiement
        $plan_id = 0;
        if (isset($explode_payment_ref[1])) {
            $plan_id = $explode_payment_ref[1];
        }


        //dd(request()->session()->get('sms'));

        $headers = [
            'Api-key' => request()->session()->get('sms')->api_key,
            'Content-Type' => 'application/json',
        ];
        $postdata = [""];
        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        $_response = $client->get(env('SMS_ADMIN_API_URL') . '/userbuyplan', [
            'headers' => $headers,
            'body' => json_encode($postdata)
        ]);
        

        $_result = json_decode($_response->getBody()->getContents());
        //dd($_result);
        $data = collect($_result); //convertir en collection afin de faire les recherche dedans

        //dd($data['plans']);


        //je verifie si le plan est reconnu dans notre system
        //$plan_id=$request->input('id');

        $plan = collect($data['plans'])->first(function ($item) use ($plan_id) {
            return $item->id == $plan_id;
        });

        if (empty($plan)) {
            $output['success'] = 0;
            $output['msg'] = "Plan SMS nos reconnu dans notre système";
            return redirect()->action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'buycredit'])->with('status', $output);
        }

        //dd($plan->sms->credits);
        /**********'''''''''* */
        //dd($package);
        //on retrouve l'unité principale en jour
        /*
            //on ne calcul plus manuellement car on peut tout recuperrer automatiquement
            $unit = 0;
            if ($package->interval == 'days') {
                //$unit = $package->interval_count;
            } elseif ($package->interval == 'months') {
                //$unit = $package->interval_count * 30;
            } elseif ($package->interval == 'years') {
                //$unit = $package->interval_count * 365;
            }
            */
        $unit_id = env('ADMINISTRATOR_SMS_PRODUCT_UNITDAYS');
        $sub_unit_id = env('ADMINISTRATOR_SMS_PRODUCT_UNITMONTHS');

        $unit = Unit::with(['sub_units'])
            ->findOrFail($sub_unit_id);
        //dd($unit->base_unit_multiplier);
        //dd($unit_id);

        //ON RECUPERE LE PRODUIT QUI FAIT l4OBJECT DE LA FACTURATION DES ABONNEMENT LOGICIEL
        $product = Product::where('id', env('ADMINISTRATOR_SMS_PRODUCT'))
            ->where('business_id', env('ADMINISTRATOR_BUSINESS'))->first();
        //dd($product);
        //$product->sub_unit_ids;

        $query = Product::join('variations', 'products.id', '=', 'variations.product_id')
            ->active()
            ->whereNull('variations.deleted_at')
            ->leftjoin('units as U', 'products.unit_id', '=', 'U.id')
            ->leftjoin(
                'variation_location_details AS VLD',
                function ($join) use ($admin_businesslocation_saas_id) {
                    $join->on('variations.id', '=', 'VLD.variation_id');

                    //Include Location
                    if (!empty($admin_businesslocation_saas_id)) {
                        $join->where(function ($query) use ($admin_businesslocation_saas_id) {
                            $query->where('VLD.location_id', '=', $admin_businesslocation_saas_id);
                            //Check null to show products even if no quantity is available in a location.
                            //TODO: Maybe add a settings to show product not available at a location or not.
                            $query->orWhereNull('VLD.location_id');
                        });
                    }
                }
            );

        $price_group_id = env('ADMINISTRATOR_SUBSCRIPTION_DEFAULTPRICEGROUP');
        if (!empty($price_group_id)) {
            $query->leftjoin(
                'variation_group_prices AS VGP',
                function ($join) use ($price_group_id) {
                    $join->on('variations.id', '=', 'VGP.variation_id')
                        ->where('VGP.price_group_id', '=', $price_group_id);
                }
            );
        }

        $query->where('products.business_id', $admin_business->id)
            ->where('products.type', '!=', 'modifier');
        $query->where('products.id',  env('ADMINISTRATOR_SMS_PRODUCT'));
        //$query->ForLocation($admin_businesslocation_saas_id);


        $query->select(
            'products.id as product_id',
            'products.name',
            'products.type',
            'products.enable_stock',
            'products.sub_unit_ids',
            'variations.id as variation_id',
            'variations.name as variation',
            'VLD.qty_available',
            'variations.sell_price_inc_tax as selling_price',
            'variations.sub_sku',
            'U.short_name as unit'

        );
        $product = $query->orderBy('VLD.qty_available', 'desc')
            ->first();
        //dd($product);

        //dd(env('ADMINISTRATOR_COMMISSION_CUSTOMLABEL'));
        $ADMINISTRATOR_COMMISSION_CUSTOMLABEL = env('ADMINISTRATOR_COMMISSION_CUSTOMLABEL');
        //dd($contact->$ADMINISTRATOR_COMMISSION_CUSTOMLABEL);





        $total_payable = (float) $plan->amount * env('ADMINISTRATOR_SUBSCRIPTION_RATE_CONVERSION');
        //on retrouve le prix unitaire d'un seul sms en divisant par le nombre de crédit
        $plan->amount = $plan->amount / $plan->sms->credits;












        //on met a note de vente
        $note = "CREDIT SMS : PAQUET NDIA-KA BUSINESS (70$/MOIS)(15/07/2024 00:00 à 15/07/2024 00:00)[#uLigHO0do92F243819740311]";
        $note = "CREDIT SMS : " . $plan->name . " (" . $plan->name . " à " . $plan->name . ")[#" . $mmpl->order_number . "]";
        $note = "CREDIT SMS : " . $plan->name . " [#" . $mmpl->order_number . "]";




        //creation de l'Object request
        $_request = new Request();
        $_request->setMethod('POST');

        $_request->replace([
            '_token' => $csrf_token,

            'is_direct_sale' => true, //pour eviter que la requete soit bloquée si la caisse est fermée
            //{{--  personnalize custom code 15072024-AUTOSELL -- 15072024}}
            //pour forcer la requete de retourner les données custom_code 15072024
            'get_direct_response' => true,
            //pour surpasser la dependance des variables stoqué en session
            $csrf_token . 'user_business_id' => $user_business_id,
            $csrf_token . 'user_id' => $user_id,
            $csrf_token . 'business_sales_cmsn_agnt' => $business_sales_cmsn_agnt,
            $csrf_token . 'business_enable_rp' => $business_enable_rp,
            $csrf_token . 'business_accounting_method' => $business_accounting_method,
            /***************************************************************** */

            "location_id" => env('ADMINISTRATOR_BUSINESSLOCATION_SAAS'),
            "sub_type" => null,
            "contact_id" => $contact->id,
            "search_product" => null,
            "invoice_layout_id" => env('ADMINISTRATOR_SUBSCRIPTION_INVOICELAYOUT'),
            "pay_term_number" => null,
            "pay_term_type" => null,
            "commission_agent" => $contact->$ADMINISTRATOR_COMMISSION_CUSTOMLABEL,
            "transaction_date" => now()->format(env('ADMINISTRATOR_SUBSCRIPTION_DATEFORMAT')),
            "exchange_rate" => "1",
            "hidden_price_group" => null,
            "default_price_group" => env('ADMINISTRATOR_SUBSCRIPTION_DEFAULTPRICEGROUP'),
            "types_of_service_id" => null,
            "types_of_service_price_group" => null,
            "sell_price_tax" => "includes",
            "products" => [
                [
                    "product_type" => $product['type'],
                    "unit_price" =>  $plan->amount,
                    "line_discount_type" => "fixed",
                    "line_discount_amount" => "0.00",
                    "item_tax" => "0.00",
                    "tax_id" => null,
                    "sell_line_note" => $note,
                    "product_id" => $product['product_id'],
                    "variation_id" => $product['variation_id'],
                    "enable_stock" => $product['enable_stock'],
                    "quantity" => $plan->sms->credits,
                    "product_unit_id" => $unit_id,
                    "sub_unit_id" => $sub_unit_id,
                    "base_unit_multiplier" => $unit->base_unit_multiplier,
                    "unit_price_inc_tax" => $plan->amount,
                ]
            ],
            "discount_type" => null,
            "discount_amount" => "0.00", //pour le coupon
            "rp_redeemed" => "0",
            "rp_redeemed_amount" => "0",
            "tax_rate_id" => null,
            "tax_calculation_amount" => "0.00",
            "shipping_details" => null,
            "shipping_address" => null,
            "shipping_status" => null,
            "delivered_to" => null,
            "delivery_person" => null,
            "shipping_charges" => "0.00",
            "round_off_amount" => "0",
            "advance_balance" => "0.0000",
            "payment" => [
                [
                    "amount" => $total_payable,
                    "method" => "cash",
                    "account_id" => env('ADMINISTRATOR_SUBSCRIPTION_CAISSEACCOUNT_ID'),
                    "card_number" => null,
                    "card_holder_name" => null,
                    "card_transaction_number" => null,
                    "card_type" => "credit",
                    "card_month" => null,
                    "card_year" => null,
                    "card_security" => null,
                    "cheque_number" => null,
                    "bank_account_number" => null,
                    "transaction_no_1" => null,
                    "transaction_no_2" => null,
                    "transaction_no_3" => null,
                    "transaction_no_4" => null,
                    "transaction_no_5" => null,
                    "transaction_no_6" => null,
                    "transaction_no_7" => null,
                    "note" => $mmpl->method . " - " . $mmpl->order_number,
                ],
                "change_return" =>  [
                    "method" => "cash",
                    "account_id" => null,
                    "card_number" => null,
                    "card_holder_name" => null,
                    "card_transaction_number" => null,
                    "card_type" => "credit",
                    "card_month" => null,
                    "card_year" => null,
                    "card_security" => null,
                    "cheque_number" => null,
                    "bank_account_number" => null,
                    "transaction_no_1" => null,
                    "transaction_no_2" => null,
                    "transaction_no_3" => null,
                    "transaction_no_4" => null,
                    "transaction_no_5" => null,
                    "transaction_no_6" => null,
                    "transaction_no_7" => null,
                ]
            ],


            "sale_note" => $note,
            "staff_note" => $mmpl->method . " - " . $mmpl->order_number,
            "change_return" => "0.00",
            "additional_notes" => $note,
            "is_suspend" => "0",
            "recur_interval" => null,
            "recur_interval_type" => "days",
            "recur_repetitions" => null,
            "subscription_repeat_on" => null,
            "size" => "all",
            "is_enabled_stock" => null,
            "is_credit_sale" => "0",
            "final_total" => $total_payable,
            "discount_type_modal" => 'fixed',
            "discount_amount_modal" => "0.00", ///discount coupon
            "rp_redeemed_modal" => null,
            "order_tax_modal" => null,
            "shipping_details_modal" => null,
            "shipping_address_modal" => null,
            "shipping_charges_modal" => "0",
            "shipping_status_modal" => null,
            "delivered_to_modal" => null,
            "delivery_person_modal" => null,
            "status" => "final"
        ]);

        //dd($_request);

        //on enregistre les variable de session, car il n'est pas present dans l'object request créée programmatiicallyt
        //mais avant de changer les variables de session on sauvegarde dabord la session source
        $__user = request()->session()->get('user');
        $__business = request()->session()->get('business');
        $__user_id = request()->session()->get('user.id');


        //changement des variable de sessions
        //$_request->setLaravelSession(session());
        /*
        $user = User::where('id', env('ADMINISTRATOR_SUBSCRIPTION_USERAGENT'))->first();
        $_request->session()->put('user', $user);
        $_request->session()->put('user.id', $user->id);
        $_request->session()->put('user.business_id', env('ADMINISTRATOR_BUSINESS'));
        $_request->session()->put('business', $admin_business);

        //$request->session()->get('business.sales_cmsn_agnt'); 
        */


        //$result=\App::call('App\Http\Controllers\OpeningStockController@save',[$_request]);
        //dd(111);

        //dd($_request);
        $result['invoiceurl'] = "error generate facture";
        try {
            $save_session = request()->session()->get('business');

            $_request->setLaravelSession(session());
            $_request->session()->put('business', $admin_business);

            $result = \App::makeWith(\App\Http\Controllers\SellPosController::class)->store($_request);

            session()->put('business', $save_session);
        } catch (\Throwable $th) {
            //dd('error');
        }

        //dd($result);
        //dd($result($_response->getBody()->getContents()));
        //dd(22);
        //dd($result);
        //dd($csrf_token."<---->".$result);
        //dd(1);

        //on remet les variables de sessions
        /*
        session()->put('user', $__user);
        session()->put('user.id', $__user_id);
        session()->put('user.business_id', $__business->id);
        session()->put('business', $__business);
        
        /*****_______________________________________________________________************/





        //on recupere le api_key depuis l'api appelant pour etre sur de l'exctitude
        $headers = [
            'Api-key' => env('SMS_ADMIN_API_KEY'),
            'Content-Type' => 'application/json',
        ];
        $postdata = ["username" => "USER_H360_" . $business_id];
        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        $_response = $client->get(env('SMS_ADMIN_API_URL') . '/users', [
            'headers' => $headers,
            'body' => json_encode($postdata)
        ]);
        $_sms_users = json_decode($_response->getBody()->getContents());
        $sms_users = collect($_sms_users); //convertir en collection afin de faire les recherche dedans

        if (empty($sms_users)) {
            dd("impossible d'utiliser les sms car vous n'avez pas des representant");
        }
        //dd($sms_users[0]);
        request()->session()->put('sms', $sms_users[0]);


        //dd(request()->session()->get('sms'));

        $headers = [
            'Api-key' => request()->session()->get('sms')->api_key,
            'Content-Type' => 'application/json',
        ];
        $postdata = [
            'plan_id' => $plan_id,
            'payment_ref' => $mmpl->method . " - " . $mmpl->order_number,
            'invoice_url' => $result['invoiceurl'],
            //'invoice_url' => "h360 url",
        ];
        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        $_response = $client->get(env('SMS_ADMIN_API_URL') . '/userbuyplan', [
            'headers' => $headers,
            'body' => json_encode($postdata)
        ]);
        //echo env('SMS_ADMIN_API_URL') . '/userbuyplan';
        //dd($headers);
        //dd($_response->getBody()->getContents());

        //dd(json_decode($_response->getBody()->getContents()));
        //dd($_response);

        $_result = json_decode($_response->getBody()->getContents());
        //dd($_result);

        $output = [
            'success' => 1,
            'msg' => "Merci , le paiement effectué avec succes",
        ];
        return redirect()->action([\Modules\ProToolsKit\Http\Controllers\SMSController::class, 'index'])->with('status', $output);

        //dd($result['invoiceurl']);
        return $result;



        dd($product);
        dd($contacts);
        //----------------------- END  custom_code-----------------------------------//////



    }
    /**
     * Confirm for flexpay gateway
     * when payment gateway is Flexpay payment gateway request package_id
     * is transaction_id & merchant_reference in session contains
     * the package_id.
     *
     * @return Response
     */
    public function confirm_flexpayBuycredit(Request $request)
    {


        $output = [

            'success' => 0,

            'msg' => 'en attente',

            'data' => []

        ];
        $http_code = 200;
        $fexpay_merchant = env('FLEXPAY_MERCHANT');
        $fexpay_token = env('FLEXPAY_TOKEN');

        $mmpls = [];
        if (!empty($request->ordernumber)) {
            $mmpls = MobilemoneyPayLine::where('order_number', '=', $request->ordernumber)
                ->where('payment_ref', 'LIKE', 'smscredit-%')
                ->whereIn('method', ['flexpay'])
                ->whereIn('status', ['pending', 'draft'])
                ->get();
        } else {
            $mmpls = MobilemoneyPayLine::where('payment_ref', 'LIKE', 'smscredit-%')
                ->whereIn('method', ['flexpay'])
                ->whereIn('status', ['pending', 'draft'])
                ->get();
        }




        if (empty($mmpls)) {
            $output = [

                'success' => 0,

                'msg' => 'Aucune transaction trouvé',

                'data' => []

            ];
            return $output;
        }

        $log = [];

        foreach ($mmpls as $key => $mmpl) {

            // je recuper l'etat de paiement
            $gateway = "https://backend.flexpay.cd/api/rest/v1/check/" . $mmpl->order_number;
            //dd($gateway );

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $gateway);
            //curl_setopt($ch, CURLOPT_GET, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $fexpay_token));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

            $response = curl_exec($ch);
            $jsonRes = json_decode($response);
            $log[] = $jsonRes;



            if (isset($jsonRes->transaction->status)) {

                if ($jsonRes->transaction->status == 0) { //transaction reussi



                    //on verifie qu'il s'agit bien d'un objet venat de notre systeme pas un hack
                    $payment_id = $request->ordernumber;
                    //on passe le paiement
                    /****************************/

                    $output = [
                        'success' => 1,
                        'msg' => "Le paiement effectué avec succes",
                    ];

                    $currency = $jsonRes->transaction->currency;
                    $montant_ht = $jsonRes->transaction->amount;
                    $montant_tt = $jsonRes->transaction->amountCustomer;
                    $frais = $montant_tt - $montant_ht;
                    $op = $jsonRes->transaction->channel;
                    $ref = $jsonRes->transaction->reference;
                    $msg = str_replace("Le paiement ", "Le paiement <$ref> ", $jsonRes->message);

                    $output = [
                        'success' => -1,
                        'msg' => $msg . "\nMontant : $montant_ht $currency \nFrais: $frais $currency \nOpérateur: $op",
                        'data' => $jsonRes
                    ];

                    //{{--  personnalize custom_code 25042024-MOBILEMONEY -- 25042024}}



                    $explode_payment_ref = explode('-', $mmpl->payment_ref); //on explode payment_ref pour recuperer package_id(1) business_id(2) user_id(3)  et le code coupn(4) , et orderNumber(5) si il y en a
                    $user_id = null;
                    try {
                        $user_id = $explode_payment_ref[3];
                    } catch (\Throwable $th) {
                        //throw $th;
                    }

                    $coupon_code = null;
                    if (isset($explode_payment_ref[4])) {
                        $coupon_code = $explode_payment_ref[4];
                    }

                    $orderNumber = null;
                    if (isset($explode_payment_ref[5])) {
                        $orderNumber = $explode_payment_ref[5];
                    }


                    //on ne gere pas encore le coupon, donc c'est null et puis le montant vient directement de la base de donnée sans subir des modifications
                    //dd($user_id);

                    //==>**//$subscription = $this->_add_subscription($coupon_code, $mmpl->amount, $mmpl->business_id, $mmpl->package_id, 'flexpay', $mmpl->payment_ref, $user_id);
                    //-->on lui ajoute ses sms 


                    $mmpl->status = 'final';
                    $mmpl->additional_notes = $msg;
                    //$mmpl->status = 'pending'; //a effacer car c'est juste pour le test
                    $mmpl->update();
                    //----------------------- END PERSONNALIZE custom_code-----------------------------------//////

                    /*
                    $package = Package::active()->find($mmpl->package_id);
                    $note = "Package : " . $package->name . "(" . $subscription->start_date->format(env('ADMINISTRATOR_SUBSCRIPTION_DATEFORMAT')) .
                        " à " .
                        $subscription->start_date->format(env('ADMINISTRATOR_SUBSCRIPTION_DATEFORMAT')) . ")" . 
                        "[#" . $mmpl->order_number . "]";
                        */
                    $note = "sera modifier automatiquement à l'interieur de la fonction appelée";


                    $result = $this->addSellPos($mmpl, $coupon_code, $note);
                    $http_code = 200;
                } elseif ($jsonRes->transaction->status == 1) {

                    //DB::commit();

                    $output = [
                        'success' => 0,
                        'msg' => 'desolé, Le paiement n\'a pas abouti'
                    ];

                    $currency = $jsonRes->transaction->currency;
                    $montant_ht = $jsonRes->transaction->amount;
                    $montant_tt = $jsonRes->transaction->amountCustomer;
                    $frais = $montant_tt - $montant_ht;
                    $op = $jsonRes->transaction->channel;
                    $ref = $jsonRes->transaction->reference;
                    $msg = str_replace("Le paiement ", "Le paiement <$ref> ", $jsonRes->message);

                    $output = [
                        'success' => -1,
                        'msg' => $msg . "\nMontant : $montant_ht $currency \nFrais: $frais $currency \nOpérateur: $op",
                        'data' => $jsonRes
                    ];


                    //{{--  personnalize custom_code 25042024-MOBILEMONEY -- 25042024}}
                    $mmpl->status = 'failed';
                    $mmpl->additional_notes = $msg;
                    $mmpl->update();
                    //----------------------- END PERSONNALIZE custom_code-----------------------------------//////
                    $http_code = 200;
                } else {
                    //paiement en attente
                    //$jsonRes->transaction->status == 2
                    $http_code = 200;
                    continue;
                }
            } else {
                //si la transaction reste toujour en pending au dela de 3 jours on le cancell
                if ($mmpl->created_at->diffInDays() > 3) {
                    $mmpl->status = 'canceled';
                    $mmpl->update();
                }
                $http_code = 200;
            }
        }


        return response()->json($output, $http_code);
        dd($log);

        dd($mmpls);
        return 11111;
    }
}
