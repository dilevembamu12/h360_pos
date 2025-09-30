<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Business;
use App\System;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Entities\SuperadminCoupon;
use Modules\Superadmin\Notifications\SubscriptionOfflinePaymentActivationConfirmation;
use Notification;
use Paystack;
use Pesapal;
use Razorpay\Api\Api;
use Srmklive\PayPal\Services\ExpressCheckout;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

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


use App\TaxRate;

/********************** */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\OpeningStockController;

/************************** */
/**************************************************** */


class SubscriptionController extends BaseController
{
    protected $provider;

    public function __construct(ModuleUtil $moduleUtil = null)
    {
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }

        if (!defined('CURLOPT_SSLVERSION')) {
            define('CURLOPT_SSLVERSION', 6);
        }

        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin.access_package_subscriptions')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Get active subscription and upcoming subscriptions.
        $active = Subscription::active_subscription($business_id);

        $nexts = Subscription::upcoming_subscriptions($business_id);
        $waiting = Subscription::waiting_approval($business_id);


        $packages = Package::active()->orderby('sort_order')->get();

        /*
        $packages = Package::active()
        //->where('description', 'LIKE', '%__business__'.$business_id)
        ->whereNot('description', 'like', '%__business__%')
        ->orderby('sort_order')->get();
        */

        /*********** custom_code 10062024 ******************/
        //filtrer les packages selon les entreprises

        foreach ($packages as $key => $package) {
            //si il contient le terme __business__0 donc il n'est peut pas etre visible
            $search = '__business__';
            if (preg_match("/{$search}/i", $package->description)) {
                /*** JE VERIFIE SI L'ID DE L'ENTREPRISE EST CONTENU DEDANS */
                $_package_business_ids = explode('__business__', $package->description)[1];
                $package_business_ids = explode('_', $_package_business_ids);

                //si l'entreprise par son business_id ne se retrouve pas dans les business pris en charge par ce paquet, on l'oubli
                if (!in_array($business_id, $package_business_ids)) {
                    $packages->forget($key);
                }
            }
            $package->description = explode('__business__', $package->description)[0]; //enleve les metadonnée apres __business__
        }
        /*************************************************** */

        //Get all module permissions and convert them into name => label
        $permissions = $this->moduleUtil->getModuleData('superadmin_package');
        $permission_formatted = [];
        foreach ($permissions as $permission) {
            foreach ($permission as $details) {
                $permission_formatted[$details['name']] = $details['label'];
            }
        }

        $intervals = ['days' => __('lang_v1.days'), 'months' => __('lang_v1.months'), 'years' => __('lang_v1.years')];

        return view('superadmin::subscription.index')
            ->with(compact('packages', 'active', 'nexts', 'waiting', 'permission_formatted', 'intervals'));
    }

    /**
     * Show pay form for a new package.
     *
     * @return Response
     */
    public function pay(Request $request, $package_id, $form_register = null)
    {
        if (!auth()->user()->can('superadmin.access_package_subscriptions')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();

            $business_id = request()->session()->get('user.business_id');

            $package = Package::active()->find($package_id);

            //Check if superadmin only package
            if ($package->is_private == 1 && !auth()->user()->can('superadmin')) {
                $output = ['success' => 0, 'msg' => __('superadmin::lang.not_allowed_for_package')];

                return redirect()
                    ->back()
                    ->with('status', $output);
            }

            //Check if one time only package
            if (empty($form_register) && $package->is_one_time) {
                $count_subcriptions = Subscription::where('business_id', $business_id)
                    ->where('package_id', $package_id)
                    ->count();

                if ($count_subcriptions > 0) {
                    $output = ['success' => 0, 'msg' => __('superadmin::lang.maximum_subscription_limit_exceed')];

                    return redirect()
                        ->back()
                        ->with('status', $output);
                }
            }

            //Check for free package & subscribe it.
            if ($package->price == 0) {
                $gateway = null;
                $payment_transaction_id = 'FREE';
                $user_id = request()->session()->get('user.id');

                $this->_add_subscription(null, 0, $business_id, $package, $gateway, $payment_transaction_id, $user_id);

                DB::commit();



                if (empty($form_register)) {
                    $output = ['success' => 1, 'msg' => __('lang_v1.success')];

                    return redirect()
                        ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index'])
                        ->with('status', $output);
                } else {
                    $output = ['success' => 1, 'msg' => __('superadmin::lang.registered_and_subscribed')];

                    return redirect()
                        ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index'])
                        ->with('status', $output);
                }
            }

            $gateways = $this->_payment_gateways();

            $system_currency = System::getCurrency();

            DB::commit();

            if (empty($form_register)) {
                $layout = 'layouts.app';
            } else {
                $layout = 'layouts.auth';
            }

            $user = request()->session()->get('user');

            $offline_payment_details = System::getProperty('offline_payment_details');

            // coupon related code
            $coupon_status = ['status' => '', 'msg' => ""];
            $package_price_after_discount = 0;
            $discount_amount = 0;
            //  check code in request
            if ($request->has('code')) {
                $coupon = SuperadminCoupon::where('coupon_code', $request->code)->first();
                // check coupon fount or not
                if ($coupon) {

                    $package_ids = json_decode($coupon->applied_on_packages);
                    $business_ids = json_decode($coupon->applied_on_business);
                    $current_date = Carbon::now()->toDateString();
                    // check all condition 
                    if (($coupon->is_active == 1) && ((is_array($package_ids) && in_array($package_id, $package_ids)) || is_null($coupon->applied_on_packages)) && ((is_array($business_ids) && in_array($business_id, $business_ids)) || is_null($coupon->applied_on_business)) &&  (Carbon::parse($coupon->expiry_date)->greaterThanOrEqualTo($current_date) || is_null($coupon->expiry_date))) {
                        // check discount type and calculate amount after discount
                        if ($coupon->discount_type == 'fixed') {
                            $discount_amount = $coupon->discount;
                            $package_price_after_discount = (float)$package->price - $coupon->discount;
                        } elseif ($coupon->discount_type == 'percentage') {

                            $discount_amount = $package->price * ($coupon->discount / 100);
                            $package_price_after_discount =  (float) $package->price - $discount_amount;
                        }

                        // after discount if package price <= 0
                        if ($package_price_after_discount <= 0) {
                            $gateway = null;
                            $payment_transaction_id = 'FREE';
                            $user_id = request()->session()->get('user.id');



                            /**********CUSTOM_CODE AJOUTE LA VENTE DANS LE LIEU DE GESTION DE LENTEPRISE */
                            $mmpl = new MobilemoneyPayLine();
                            $mmpl->business_id = $business_id;
                            $mmpl->package_id = $package_id;

                            //je verifie d'abor si l'entreprise a un contact lié afin que la facture soit generée en son nom
                            // sinon on lui dit d'informer l'administrateur
                            $contact = Contact::where('business_id', env('ADMINISTRATOR_BUSINESS'))
                                ->where(env('ADMINISTRATOR_CONTACT_CUSTOMLABEL'), $mmpl->business_id)->first();
                            if (empty($contact)) {
                                $coupon_status = ['status' => 'danger', 'msg' => "**Entreprise pas encore representée , veuillez contacter les administrateurs de H360 pour vous créer un representant"];
                                return view('superadmin::subscription.pay')
                                    ->with(compact('package', 'gateways', 'system_currency', 'layout', 'user', 'offline_payment_details', 'coupon_status', 'package_price_after_discount', 'discount_amount'));
                            }

                            $subscription = $this->_add_subscription($request->code, 0, $business_id, $package_id, $gateway, $payment_transaction_id, $user_id);

                            $note = "Package : " . $package->name . "(" . $subscription->start_date->format(env('ADMINISTRATOR_SUBSCRIPTION_DATEFORMAT')) .
                                " à " .
                                $subscription->end_date->format(env('ADMINISTRATOR_SUBSCRIPTION_DATEFORMAT')) . ")" . "[#" . $subscription->payment_transaction_id . "]";


                            $result = $this->addSellPos($mmpl, $request->code, $note);
                            /***************************************************************************** */

                            return redirect()
                                ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index'])
                                ->with('status', ['success' => 1, 'msg' => __('lang_v1.success')]);
                        }

                        $coupon_status = ['status' => 'success', 'msg' => "successfull"];
                    } else {
                        // check deactive
                        if ($coupon->is_active == 0) {
                            $coupon_status = ['status' => 'danger', 'msg' => __('superadmin::lang.coupon_is_deactive')];
                        }
                        // check coupon with this package
                        else if ((is_array($package_ids) && !in_array($package_id, $package_ids)) && !is_null($coupon->applied_on_packages)) {

                            $coupon_status = ['status' => 'danger', 'msg' => __('superadmin::lang.coupon_not_matched_with_package')];
                        }
                        // check coupon with this business
                        else if ((is_array($business_ids) && !in_array($business_id, $business_ids)) && !is_null($coupon->applied_on_business)) {

                            $coupon_status = ['status' => 'danger', 'msg' => __('superadmin::lang.coupon_not_matched_with_business')];
                        }
                        //  check expiry date
                        else if (Carbon::parse($current_date)->greaterThanOrEqualTo($coupon->expiry_date) && !is_null($coupon->expiry_date)) {

                            $coupon_status = ['status' => 'danger', 'msg' => __('superadmin::lang.coupon_expired')];
                        }
                    }
                } else {
                    $coupon_status = ['status' => 'danger', 'msg' => __('superadmin::lang.invalid_coupon')];
                }
            }
            return view('superadmin::subscription.pay')
                ->with(compact('package', 'gateways', 'system_currency', 'layout', 'user', 'offline_payment_details', 'coupon_status', 'package_price_after_discount', 'discount_amount'));
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = ['success' => 0, 'msg' => 'File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage()];


            return redirect()
                ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index'])
                ->with('status', $output);
        }
    }

    /**
     * Show pay form for a new package.
     *
     * @return Response
     */
    public function registerPay($package_id, Request $request)
    {
        return $this->pay($request, $package_id, 1);
    }

    /**
     * Save the payment details and add subscription details
     *
     * @return Response
     */
    public function trigger_flexpay($package_id, Request $request)
    {
       

        $business_id = request()->session()->get('user.business_id');
        $business_name = request()->session()->get('business.name');
        $user_id = request()->session()->get('user.id');
        $package = Package::active()->find($package_id);

        //dd($this->_payment_gateways());
        //dd($request->gateway);

        /******* custom_code 09062024  FLEXPAY_PAIMENT */
        //on gerere flexpay en verifiant d'abord que le client a choisi de payer via flexpay
        //dd($request->phone);
        //dd($this->_payment_gateways());

        if (isset($this->_payment_gateways()['flexpay']) && !empty($request->phone)) {
            //return 7410;
            return $this->pay_flexpay($business_id, $business_name, $package, $request);
        }

        if (isset($this->_payment_gateways()['flexpay']) && !empty($request->gateway_type=="flexpay_bank")) {
            //return 7410;
            return $this->pay_flexpayBank($business_id, $business_name, $package, $request);
        }

        

        $output = [
            'success' => 0,
            'msg' => '**error, veuillez réessayer svp!!',
        ];
        return back()->with('status', $output);
        return "error, veuillez réessayer svp";
        /********************************************** */
    }
    /**
     * Save the payment details and add subscription details
     *
     * @return Response
     */
    public function confirm($package_id, Request $request)
    {
        if (!auth()->user()->can('superadmin.access_package_subscriptions')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            //Disable in demo
            if (config('app.env') == 'demo') {
                $output = [
                    'success' => 0,
                    'msg' => 'Feature disabled in demo!!',
                ];

                return back()->with('status', $output);
            }



            //Confirm for pesapal payment gateway
            if (isset($this->_payment_gateways()['pesapal']) && (strpos($request->merchant_reference, 'PESAPAL') !== false)) {
                return $this->confirm_pesapal($package_id, $request);
            }

            if (!isset($this->_payment_gateways()['offline'])) {
                return "error";
            }





            $business_id = request()->session()->get('user.business_id');
            $business_name = request()->session()->get('business.name');
            $user_id = request()->session()->get('user.id');
            $package = Package::active()->find($package_id);

            DB::beginTransaction();

            //Call the payment method
            $pay_function = 'pay_' . request()->gateway;

            $payment_transaction_id = null;
            if (method_exists($this, $pay_function)) {
                $payment_transaction_id = $this->$pay_function($business_id, $business_name, $package, $request);
            }
            //Add subscription details after payment is succesful
            $this->_add_subscription(request()->coupon_code, request()->price, $business_id, $package_id, request()->gateway, $payment_transaction_id, $user_id);
            DB::commit();

            $msg = __('lang_v1.success');
            if (request()->gateway == 'offline') {
                $msg = __('superadmin::lang.notification_sent_for_approval');
            }
            $output = ['success' => 1, 'msg' => $msg];
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            echo 'File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage();
            exit;
            $output = ['success' => 0, 'msg' => $e->getMessage()];
        }

        return redirect()
            ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index'])
            ->with('status', $output);
    }

    protected function check_coupon($coupon_code, $package, $business_id)
    {

        $coupon = null;
        if (isset($coupon_code)) {
            $coupon = SuperadminCoupon::where('coupon_code', $coupon_code)->first();
        }
        /***** verification securité sur le coupon *******/
        //1) SI IL EST ACTIF
        //2) SI LA DATE D'EXIPRATION N'EST PAS DEPASSEE
        //3) SI IL EST LIE A CE PACKAGE
        //4) SI IL EST LIE A CE BUSINESS
        $package_ids = json_decode($coupon->applied_on_packages);
        $business_ids = json_decode($coupon->applied_on_business);
        $current_date = Carbon::now()->toDateString();
        $coupon_status=[];
        // check all condition 
        if (($coupon->is_active == 1) && ((is_array($package_ids) && in_array($package->id, $package_ids)) || is_null($coupon->applied_on_packages)) && ((is_array($business_ids) && in_array($business_id, $business_ids)) || is_null($coupon->applied_on_business)) &&  (Carbon::parse($coupon->expiry_date)->greaterThanOrEqualTo($current_date) || is_null($coupon->expiry_date))) {
            // check discount type and calculate amount after discount
            if ($coupon->discount_type == 'fixed') {
                $discount_amount = $coupon->discount;
                $package_price_after_discount = (float)$package->price - $coupon->discount;
            } elseif ($coupon->discount_type == 'percentage') {

                $discount_amount = $package->price * ($coupon->discount / 100);
                $package_price_after_discount =  (float) $package->price - $discount_amount;
            }

            // after discount if package price <= 0

        } else {
            // check deactive
            
            if ($coupon->is_active == 0) {
                $coupon_status = ['status' => 'danger', 'msg' => __('superadmin::lang.coupon_is_deactive')];
            }
            // check coupon with this package
            else if ((is_array($package_ids) && !in_array($package->id, $package_ids)) && !is_null($coupon->applied_on_packages)) {

                $coupon_status = ['status' => 'danger', 'msg' => __('superadmin::lang.coupon_not_matched_with_package')];
            }
            // check coupon with this business
            else if ((is_array($business_ids) && !in_array($business_id, $business_ids)) && !is_null($coupon->applied_on_business)) {

                $coupon_status = ['status' => 'danger', 'msg' => __('superadmin::lang.coupon_not_matched_with_business')];
            }
            //  check expiry date
            else if (Carbon::parse($current_date)->greaterThanOrEqualTo($coupon->expiry_date) && !is_null($coupon->expiry_date)) {

                $coupon_status = ['status' => 'danger', 'msg' => __('superadmin::lang.coupon_expired')];
            }
        }
        return $coupon_status;
        //si il n'a pas d'erreur
    }

    /**
     * Trigger for flexpay gateway
     * when payment gateway is Flexpay payment gateway request package_id
     * is transaction_id & merchant_reference in session contains
     * the package_id.
     *
     * @return Response
     */
    protected function pay_flexpay($business_id, $business_name, $package, $request)
    {




        
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
        //dd(12);




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

        $total_payable = (float) $package->price * env('ADMINISTRATOR_SUBSCRIPTION_RATE_CONVERSION');




        //ON RECUPERE LE COUPON SI IL Y EN A
        $coupon_code = $request->input('coupon_code');
        if (!empty($coupon_code)) {
            $coupon_status = $this->check_coupon($coupon_code, $package, $business_id);
            //si il y a errur dans le coupon
            if (!empty($coupon_status)) {
                $output = [
                    'success' => 0,
                    'msg' => $coupon_status['msg']
                ];
                return \Response::json($output, 200);
            }
        }


        $payment_ref = '';
        if (isset($coupon_code)) {
            $payment_ref = 'subscription-' . $package->id . "-" . $business_id . "-" . $user_id . "-$coupon_code";
        } else {
            $payment_ref = 'subscription-' . $package->id . "-" . $business_id . "-" . $user_id;
        }

        //on recupere alors le coupon pour l'utiliser
        $coupon = SuperadminCoupon::where('coupon_code', $coupon_code)->first();
        if ($coupon->discount_type == 'fixed') {
            $discount_amount = $coupon->discount;
            $total_payable = $total_payable - $coupon->discount;
        } elseif ($coupon->discount_type == 'percentage') {

            $discount_amount = $package->price * ($coupon->discount / 100);
            $total_payable =  $total_payable - $discount_amount;
        }
        /*********************************************** */
        //dd($payment_ref );





        $url = 'http://dileve.com';
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
                if (isset($coupon_code)) {
                    $payment_ref = 'subscription-' . $package->id . "-" . $business_id . "-" . $user_id . "-$coupon_code";
                } else {
                    $payment_ref = 'subscription-' . $package->id . "-" . $business_id . "-" . $user_id;
                }
                $data = [
                    'business_id' => $business_id,
                    'currency_id' => $currency_id,
                    'package_id' => $package->id,
                    'order_number' => $jsonRes->orderNumber,
                    'payment_ref' => $payment_ref."-".$jsonRes->orderNumber,
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


        dd($total_payable);
        dd($admin_business_details->currency_code);
        dd([$fexpay_merchant, $fexpay_token]);
        return $output;



        if (empty($request->phone)) { //cad pour le test et la confirmation
            return 0; //return empty comme paiement echoué
        }
        dd(521);
        $merchant_reference = $request->merchant_reference;
        $pesapal_session = $request->session()->pull('pesapal');

        if ($pesapal_session['ref'] == $merchant_reference) {
            $package_id = $pesapal_session['package_id'];

            $business_id = request()->session()->get('user.business_id');
            $business_name = request()->session()->get('business.name');
            $user_id = request()->session()->get('user.id');
            $package = Package::active()->find($package_id);

            $this->_add_subscription($business_id, $package, 'pesapal', $transaction_id, $user_id);
            $output = ['success' => 1, 'msg' => __('superadmin::lang.waiting_for_confirmation')];

            return redirect()
                ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index'])
                ->with('status', $output);
        }
    }

    /**
     * Trigger for flexpay gateway
     * when payment gateway is Flexpay payment gateway request package_id
     * is transaction_id & merchant_reference in session contains
     * the package_id.
     *
     * @return Response
     */
    protected function pay_flexpayBank($business_id, $business_name, $package, $request)
    {

        //dd(route('login'));



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
        //dd(12);




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
        //dd($currency_id);

        $total_payable = (float) $package->price * env('ADMINISTRATOR_SUBSCRIPTION_RATE_CONVERSION');




        //ON RECUPERE LE COUPON SI IL Y EN A
        $coupon_code = $request->input('coupon_code');
        if (!empty($coupon_code)) {
            $coupon_status = $this->check_coupon($coupon_code, $package, $business_id);
            //si il y a errur dans le coupon
            if (!empty($coupon_status)) {
                $output = [
                    'success' => 0,
                    'msg' => $coupon_status['msg']
                ];
                return \Response::json($output, 200);
            }
        }


        $payment_ref = '';
        if (isset($coupon_code)) {
            $payment_ref = 'subscription-' . $package->id . "-" . $business_id . "-" . $user_id . "-$coupon_code";
        } else {
            $payment_ref = 'subscription-' . $package->id . "-" . $business_id . "-" . $user_id;
        }

        //on recupere alors le coupon pour l'utiliser
        $coupon = SuperadminCoupon::where('coupon_code', $coupon_code)->first();
        if ($coupon->discount_type == 'fixed') {
            $discount_amount = $coupon->discount;
            $total_payable = $total_payable - $coupon->discount;
        } elseif ($coupon->discount_type == 'percentage') {

            $discount_amount = $package->price * ($coupon->discount / 100);
            $total_payable =  $total_payable - $discount_amount;
        }
        /*********************************************** */
        //dd($payment_ref );



        //$total_payable= number_format((float)$total_payable, 2, '.', '');
        //$total_payable=2.058876464; 
        //dd($total_payable);
        $url = route('login')."?close_current_windows=1";
        $charge = [
            'authorization'=>"Bearer " . $fexpay_token,
            "description"=>"paiement carte",

            'amount' => $total_payable,
            "phone" => $phone,
            "reference" => $payment_ref,

            "merchant" => $fexpay_merchant,
            'currency' => strtoupper($currency_code),
            "callback_url" => $url,
            "approve_url" => $url,
            "cancel_url" => $url,
            "decline_url" => $url,
        ];
        //dd($charge);
        $data = json_encode($charge);
        $gateway = "https://cardpayment.flexpay.cd/v1.1/pay";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $gateway);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        $response = curl_exec($ch);

        //dd($response);


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
                if (isset($coupon_code)) {
                    $payment_ref = 'subscription-' . $package->id . "-" . $business_id . "-" . $user_id . "-$coupon_code";
                } else {
                    $payment_ref = 'subscription-' . $package->id . "-" . $business_id . "-" . $user_id;
                }
                $data = [
                    'business_id' => $business_id,
                    'currency_id' => $currency_id,
                    'package_id' => $package->id,
                    'order_number' => $jsonRes->orderNumber,
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


        dd($total_payable);
        dd($admin_business_details->currency_code);
        dd([$fexpay_merchant, $fexpay_token]);
        return $output;



        if (empty($request->phone)) { //cad pour le test et la confirmation
            return 0; //return empty comme paiement echoué
        }
        dd(521);
        $merchant_reference = $request->merchant_reference;
        $pesapal_session = $request->session()->pull('pesapal');

        if ($pesapal_session['ref'] == $merchant_reference) {
            $package_id = $pesapal_session['package_id'];

            $business_id = request()->session()->get('user.business_id');
            $business_name = request()->session()->get('business.name');
            $user_id = request()->session()->get('user.id');
            $package = Package::active()->find($package_id);

            $this->_add_subscription($business_id, $package, 'pesapal', $transaction_id, $user_id);
            $output = ['success' => 1, 'msg' => __('superadmin::lang.waiting_for_confirmation')];

            return redirect()
                ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index'])
                ->with('status', $output);
        }
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

        //on retrouve le package qui fait l'objet de paiement
        $package_id = $mmpl->package_id;
        $package = Package::active()->find($package_id);
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
        $unit_id = env('ADMINISTRATOR_SUBSCRIPTION_PRODUCT_UNITDAYS');
        $sub_unit_id = env('ADMINISTRATOR_SUBSCRIPTION_PRODUCT_UNIT' . strtoupper($package->interval));

        $unit = Unit::with(['sub_units'])
            ->findOrFail($sub_unit_id);
        //dd($unit->base_unit_multiplier);
        //dd($unit_id);

        //ON RECUPERE LE PRODUIT QUI FAIT l4OBJECT DE LA FACTURATION DES ABONNEMENT LOGICIEL
        $product = Product::where('id', env('ADMINISTRATOR_SUBSCRIPTION_PRODUCT'))
            ->where('business_id', env('ADMINISTRATOR_BUSINESS'))->first();
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
        $query->where('products.id',  env('ADMINISTRATOR_SUBSCRIPTION_PRODUCT'));
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


        //dd(env('ADMINISTRATOR_COMMISSION_CUSTOMLABEL'));
        $ADMINISTRATOR_COMMISSION_CUSTOMLABEL = env('ADMINISTRATOR_COMMISSION_CUSTOMLABEL');
        //dd($contact->$ADMINISTRATOR_COMMISSION_CUSTOMLABEL);





        $total_payable = (float) $package->price * env('ADMINISTRATOR_SUBSCRIPTION_RATE_CONVERSION');
        /*********** je recuper le coupon ********/
        //on recupere alors le coupon pour l'utiliser
        //si il y a errur dans le coupon
        $coupon = SuperadminCoupon::where('coupon_code', $coupon_code)->first();
        if (!empty($coupon)) {
        }
        if ($coupon->discount_type == 'fixed') {
            $discount_amount = $coupon->discount;
            $total_payable = $total_payable - $coupon->discount;
        } elseif ($coupon->discount_type == 'percentage') {

            $discount_amount = $package->price * ($coupon->discount / 100);
            $total_payable =  $total_payable - $discount_amount;

            //au niveau de facture  le $discount_amount n'accepte que le pourcentage
            $discount_amount =  $coupon->discount;
            //dd($discount_amount);
        }
        //dd($coupon);
        /**************************************** */

        
        /*********** personnalize custom la gestion des frais de retrait 12/08/2024 */
        $order_tax_modal = null;
        $tax_rate_id = null;
        $tax_calculation_amount = 0;

        $tax_rates = TaxRate::where('business_id', $admin_business->id)
            ->where('is_tax_group', '0')
            ->select(['name', 'amount', 'id', 'for_tax_group'])->get();
        foreach ($tax_rates as $key => $tax_rate) {
            $range = array_map('floatval', explode('-', explode(' ', $tax_rate->name)[2]));
            if (($total_payable > $range[0] || $total_payable == $range[0]) && ($total_payable < $range[1] || $total_payable == $range[1])) {
                $order_tax_modal = $tax_rate->id;
                $tax_rate_id = $tax_rate->id;
                $tax_calculation_amount = $tax_rate->amount;
            }
        }
        /******************************************** */
        
        
        //creation de l'Object request
        $_request = new Request();
        $_request->setMethod('POST');

        $_request->replace([
            '_token' => csrf_token(),
            'is_direct_sale' => true, //pour eviter que la requete soit bloquée si la caisse est fermée
            //{{--  personnalize custom code 15072024-AUTOSELL -- 15072024}}
            //pour forcer la requete de retourner les données custom_code 15072024
            'get_direct_response' => true,
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
                    "unit_price" =>  $package->price,
                    "line_discount_type" => "fixed",
                    "line_discount_amount" => "0.00",
                    "item_tax" => "0.00",
                    "tax_id" => null,
                    "sell_line_note" => $note,
                    "product_id" => $product['product_id'],
                    "variation_id" => $product['variation_id'],
                    "enable_stock" => $product['enable_stock'],
                    "quantity" => "1",
                    "product_unit_id" => $unit_id,
                    "sub_unit_id" => $sub_unit_id,
                    "base_unit_multiplier" => $unit->base_unit_multiplier,
                    "unit_price_inc_tax" => $package->price,
                ]
            ],
            "discount_type" => $coupon->discount_type,
            "discount_amount" => $discount_amount, //pour le coupon
            "rp_redeemed" => "0",
            "rp_redeemed_amount" => "0",
            "tax_rate_id" => $tax_rate_id,
            "tax_calculation_amount" => $tax_calculation_amount,
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
            "discount_type_modal" => $coupon->discount_type,
            "discount_amount_modal" => $discount_amount, ///discount coupon
            "rp_redeemed_modal" => null,
            "order_tax_modal" => $order_tax_modal,
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

        //changement des variable de ssions
        $_request->setLaravelSession(session());
        $_request->session()->put('user.business_id', env('ADMINISTRATOR_BUSINESS'));
        $user = User::where('id', env('ADMINISTRATOR_SUBSCRIPTION_USERAGENT'))->first();
        $_request->session()->put('user', $user);
        $_request->session()->put('business', $admin_business);

        //$request->session()->get('business.sales_cmsn_agnt');


        //$result=\App::call('App\Http\Controllers\OpeningStockController@save',[$_request]);
        $result = \App::makeWith(\App\Http\Controllers\SellPosController::class)->store($_request);
        //dd($result);
        //dd(1);

        //on remet les variables de sessions
        session()->put('user', $__user);
        session()->put('business', $__business);

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
    public function confirm_flexpaySubscription(Request $request)
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
                ->where('payment_ref', 'LIKE', 'subscription-%')
                ->whereIn('method', ['flexpay'])
                ->whereIn('status', ['pending', 'draft'])
                ->get();
        } else {
            $mmpls = MobilemoneyPayLine::where('payment_ref', 'LIKE', 'subscription-%')
                ->whereIn('method', ['flexpay'])
                ->whereIn('status', ['pending', 'draft'])
                ->get();
        }

        //dd($mmpls);
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
                    $subscription = $this->_add_subscription($coupon_code, $mmpl->amount, $mmpl->business_id, $mmpl->package_id, 'flexpay', $mmpl->payment_ref, $user_id);

                    $mmpl->status = 'final';
                    $mmpl->additional_notes = $msg;
                    $mmpl->update();
                    //----------------------- END PERSONNALIZE custom_code-----------------------------------//////

                    $package = Package::active()->find($mmpl->package_id);
                    $note = "Package : " . $package->name . "(" . $subscription->start_date->format(env('ADMINISTRATOR_SUBSCRIPTION_DATEFORMAT')) .
                        " à " .
                        $subscription->end_date->format(env('ADMINISTRATOR_SUBSCRIPTION_DATEFORMAT')) . ")" . 
                        "[#" . $mmpl->order_number . "]";


                    //$result = $this->addSellPos($mmpl, $request->code, $note);
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


    /**
     * Confirm for pesapal gateway
     * when payment gateway is PesaPal payment gateway request package_id
     * is transaction_id & merchant_reference in session contains
     * the package_id.
     *
     * @return Response
     */
    protected function confirm_pesapal($transaction_id, $request)
    {
        $merchant_reference = $request->merchant_reference;
        $pesapal_session = $request->session()->pull('pesapal');

        if ($pesapal_session['ref'] == $merchant_reference) {
            $package_id = $pesapal_session['package_id'];

            $business_id = request()->session()->get('user.business_id');
            $business_name = request()->session()->get('business.name');
            $user_id = request()->session()->get('user.id');
            $package = Package::active()->find($package_id);

            $this->_add_subscription($business_id, $package, 'pesapal', $transaction_id, $user_id);
            $output = ['success' => 1, 'msg' => __('superadmin::lang.waiting_for_confirmation')];

            return redirect()
                ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index'])
                ->with('status', $output);
        }
    }

    /**
     * Stripe payment method
     *
     * @return Response
     */
    protected function pay_stripe($business_id, $business_name, $package, $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $metadata = ['business_id' => $business_id, 'business_name' => $business_name, 'stripe_email' => $request->stripeEmail, 'package_name' => $package->name];

        $customer = Customer::create([
            'name' => 'Stripe User',
            'email' => $request->stripeEmail,
            'source' => $request->stripeToken,
            'metadata' => $metadata,
            'description' => 'Stripe payment',
        ]);

        // "address" => ["city" => $city, "country" => $country, "line1" => $address, "line2" => "", "postal_code" => $zipCode, "state" => $state]

        $system_currency = System::getCurrency();

        $charge = Charge::create([
            'amount' => $request->price * 100,
            'currency' => strtolower($system_currency->code),
            //"source" => $request->stripeToken,
            'customer' => $customer,
            'metadata' => $metadata,
        ]);

        return $charge->id;
    }

    /**
     * Offline payment method
     *
     * @return Response
     */
    protected function pay_offline($business_id, $business_name, $package, $request)
    {

        //Disable in demo
        if (config('app.env') == 'demo') {
            $output = [
                'success' => 0,
                'msg' => 'Feature disabled in demo!!',
            ];

            return back()->with('status', $output);
        }

        //Send notification
        $email = System::getProperty('email');
        $business = Business::find($business_id);

        if (!$this->moduleUtil->IsMailConfigured()) {
            return null;
        }
        $system_currency = System::getCurrency();
        $package->price = $system_currency->symbol . number_format($package->price, 2, $system_currency->decimal_separator, $system_currency->thousand_separator);

        Notification::route('mail', $email)
            ->notify(new SubscriptionOfflinePaymentActivationConfirmation($business, $package));

        return null;
    }


    /**
     * Paypal payment method - redirect to paypal url for payments
     *
     * @return Response
     */
    public function paypalExpressCheckout(Request $request)
    {
        $price = $request->input('price');
        $package_name = $request->input('package_name');

        $accessToken = $this->generatePaypalAccessToken();

        // check paypal mode 
        if (env('PAYPAL_MODE') == 'sandbox') {
            $url = config('paypal.baseURL.sandbox') . '/v2/checkout/orders';
        } else if (env('PAYPAL_MODE') == 'live') {
            $url = config('paypal.baseURL.production') . '/v2/checkout/orders';
        }
        $system_currency = System::getCurrency();
        $currency_code = $system_currency->code;


        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post($url, [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $currency_code,
                        'value' => number_format($price, 2),
                    ],
                    'description' => $package_name,
                ],
            ],
        ]);
        $data = $response->json();
        return $data;
    }

    public function capturePaypalOrder(Request $request)
    {
        try {
            $orderId = $request->input('orderID');
            $accessToken = $this->generatePaypalAccessToken();
            // check paypal mode 
            if (env('PAYPAL_MODE') == 'sandbox') {
                $url = config('paypal.baseURL.sandbox') . '/v2/checkout/orders/' . $orderId . '/capture';
            } else if (env('PAYPAL_MODE') == 'live') {
                $url = config('paypal.baseURL.production') . '/v2/checkout/orders/' . $orderId . '/capture';
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post($url, [
                'intent' => 'CAPTURE',
            ]);

            $data = $response->json();

            if ($response->successful() && $data['status'] === 'COMPLETED') {
                $price = $data['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
                $transaction_id = $data['purchase_units'][0]['payments']['captures'][0]['id'];

                $coupon_code = $request->input('coupon_code');
                $package_id = $request->input('package_id');
                $business_id = $request->input('business_id');
                $gateway = $request->input('gateway');
                $user_id = $request->input('user_id');

                if (isset($coupon_code)) {
                    $coupon_code = $coupon_code;
                } else {
                    $coupon_code = null;
                }

                $this->_add_subscription($coupon_code, $price, $business_id, $package_id, $gateway, $transaction_id, $user_id);

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
                Session::flash('status', ['success' => 1, 'msg' => __('lang_v1.success')]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    public function generatePaypalAccessToken()
    {
        // Construct the credentials
        $credentials = base64_encode(config('paypal.client_id') . ':' . config('paypal.app_secret'));

        // check paypal mode 
        if (env('PAYPAL_MODE') == 'sandbox') {
            $url = config('paypal.baseURL.sandbox') . '/v1/oauth2/token';
        } else if (env('PAYPAL_MODE') == 'live') {
            $url = config('paypal.baseURL.production') . '/v1/oauth2/token';
        }

        // Send the request to obtain the access token
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
        ])
            ->asForm()
            ->post($url, [
                'grant_type' => 'client_credentials',
            ]);

        $data = $response->json();
        $accessToken = $data['access_token'];

        return $accessToken;
    }

    /**
     * Razor pay payment method
     *
     * @return Response
     */
    protected function pay_razorpay($business_id, $business_name, $package, $request)
    {
        $razorpay_payment_id = $request->razorpay_payment_id;
        $razorpay_api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

        $payment = $razorpay_api->payment->fetch($razorpay_payment_id)->capture(['amount' => $request->price * 100]); // Captures a payment

        if (empty($payment->error_code)) {
            return $payment->id;
        } else {
            $error_description = $payment->error_description;
            throw new \Exception($error_description);
        }
    }

    /**
     * Redirect the User to Paystack Payment Page
     *
     * @return Url
     */
    public function getRedirectToPaystack()
    {
        return Paystack::getAuthorizationUrl()->redirectNow();
    }

    /**
     * Obtain Paystack payment information
     *
     * @return void
     */
    public function postPaymentPaystackCallback()
    {
        $payment = Paystack::getPaymentData();
        $business_id = $payment['data']['metadata']['business_id'];
        $package_id = $payment['data']['metadata']['package_id'];
        $gateway = $payment['data']['metadata']['gateway'];
        $payment_transaction_id = $payment['data']['reference'];
        $user_id = $payment['data']['metadata']['user_id'];
        $price = $payment['data']['amount'] / 100;

        if (isset($payment['data']['metadata']['coupon_code'])) {
            $coupon_code = $payment['data']['metadata']['coupon_code'];
        } else {
            $coupon_code = null;
        }


        if ($payment['status']) {
            //Add subscription
            $this->_add_subscription($coupon_code, $price, $business_id, $package_id, $gateway, $payment_transaction_id, $user_id);

            return redirect()
                ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index'])
                ->with('status', ['success' => 1, 'msg' => __('lang_v1.success')]);
        } else {
            return redirect()
                ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'pay'], [$package_id])
                ->with('status', ['success' => 0, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    /**
     * Obtain Flutterwave payment information
     *
     * @return response
     */
    public function postFlutterwavePaymentCallback(Request $request)
    {
        $url = 'https://api.flutterwave.com/v3/transactions/' . $request->get('transaction_id') . '/verify';
        $header = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . env('FLUTTERWAVE_SECRET_KEY'),
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $header,
        ]);
        $response = curl_exec($curl);
        curl_close($curl);

        $payment = json_decode($response, true);


        if ($payment['status'] == 'success') {
            //Add subscription
            $business_id = $payment['data']['meta']['business_id'];
            $package_id = $payment['data']['meta']['package_id'];
            $gateway = $payment['data']['meta']['gateway'];
            $payment_transaction_id = $payment['data']['tx_ref'];
            $user_id = $payment['data']['meta']['user_id'];
            $price = $payment['data']['amount'];

            if (isset($payment['data']['meta']['coupon_code'])) {
                $coupon_code = $payment['data']['meta']['coupon_code'];
            } else {
                $coupon_code = null;
            }


            $this->_add_subscription($coupon_code, $price, $business_id, $package_id, $gateway, $payment_transaction_id, $user_id);

            return redirect()
                ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'index'])
                ->with('status', ['success' => 1, 'msg' => __('lang_v1.success')]);
        } else {
            $package_id = $payment['data']['meta']['package_id'];
            return redirect()
                ->action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'pay'], [$package_id])
                ->with('status', ['success' => 0, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    /**
     * Show the specified resource.
     *
     * @return Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('superadmin.access_package_subscriptions')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = Subscription::where('business_id', $business_id)
            ->with(['package', 'created_user', 'business'])
            ->find($id);

        $system_settings = System::getProperties([
            'invoice_business_name',
            'email',
            'invoice_business_landmark',
            'invoice_business_city',
            'invoice_business_zip',
            'invoice_business_state',
            'invoice_business_country',
        ]);
        $system = [];
        foreach ($system_settings as $setting) {
            $system[$setting['key']] = $setting['value'];
        }

        return view('superadmin::subscription.show_subscription_modal')
            ->with(compact('subscription', 'system'));
    }

    /**
     * Retrieves list of all subscriptions for the current business
     *
     * @return \Illuminate\Http\Response
     */
    public function allSubscriptions()
    {
        if (!auth()->user()->can('superadmin.access_package_subscriptions')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscriptions = Subscription::where('subscriptions.business_id', $business_id)
            ->leftjoin(
                'packages as P',
                'subscriptions.package_id',
                '=',
                'P.id'
            )
            ->leftjoin(
                'users as U',
                'subscriptions.created_id',
                '=',
                'U.id'
            )
            ->addSelect(
                'P.name as package_name',
                DB::raw("CONCAT(COALESCE(U.surname, ''), ' ', COALESCE(U.first_name, ''), ' ', COALESCE(U.last_name, '')) as created_by"),
                'subscriptions.*'
            );

        return Datatables::of($subscriptions)
            ->editColumn(
                'start_date',
                '@if(!empty($start_date)){{@format_date($start_date)}}@endif'
            )
            ->editColumn(
                'end_date',
                '@if(!empty($end_date)){{@format_date($end_date)}}@endif'
            )
            ->editColumn(
                'trial_end_date',
                '@if(!empty($trial_end_date)){{@format_date($trial_end_date)}}@endif'
            )
            ->editColumn(
                'package_price',
                '<span class="display_currency" data-currency_symbol="true">{{$package_price}}</span>'
            )
            ->editColumn(
                'created_at',
                '@if(!empty($created_at)){{@format_date($created_at)}}@endif'
            )
            ->filterColumn('created_by', function ($query, $keyword) {
                $query->whereRaw("CONCAT(COALESCE(U.surname, ''), ' ', COALESCE(U.first_name, ''), ' ', COALESCE(U.last_name, '')) like ?", ["%{$keyword}%"]);
            })
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-primary btn-xs btn-modal" data-container=".view_modal" data-href="' . action([\Modules\Superadmin\Http\Controllers\SubscriptionController::class, 'show'], $row->id) . '" ><i class="fa fa-eye" aria-hidden="true"></i> ' . __('messages.view') . '</button>';
            })
            ->rawColumns(['package_price', 'action'])
            ->make(true);
    }

    public function forceActive($id)
    {

        $current_date = \Carbon::today();


        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            try {
                //Get active subscription
                $active = Subscription::active_subscription($business_id);
                if ($active) {
                    $active->end_date = $current_date->subDays(1)->toDateString();
                    $active->update();
                }

                $subscription = Subscription::find($id);
                $package = Package::find($subscription->package_id);

                //Calculate end date
                $end_date = $this->calculate_end_date($package);
                $current_date = \Carbon::today();
                $subscription->start_date = $current_date->toDateString();
                $subscription->end_date = $end_date;
                $subscription->update();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.success'),
                ];
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    public function calculate_end_date($package)
    {

        $start_date = \Carbon::today();
        if ($package->interval == 'days') {
            $end_date = $start_date->addDays($package->interval_count)->toDateString();
        } elseif ($package->interval == 'months') {
            $end_date = $start_date->addMonths($package->interval_count)->toDateString();
        } elseif ($package->interval == 'years') {
            $end_date = $start_date->addYears($package->interval_count)->toDateString();
        }

        return $end_date;
    }
}
