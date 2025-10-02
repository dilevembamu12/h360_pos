<?php
/* CREATION DES UTILISATEUR DANS L'API DE AI
CHAQUE ENTREPRISE A UN UTILISATEUR CREE AVEC IDENTIFIANT user_IdEntreprise
ET CHAQUE UTILISATEUR DE L'ENTREPRISE A SON CHATBOT
 *
 * @author     The Web Fosters <thewebfosters@gmail.com>
 * @owner      The Web Fosters <thewebfosters@gmail.com>
 * @copyright  2018 The Web Fosters
 * @license    As attached in zip file.
 */

namespace App\Http\Controllers\H360ai;

use App\Account;
use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Contact;
use App\CustomerGroup;
use App\InvoiceLayout;
use App\InvoiceScheme;
use App\Media;
use App\Product;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\TypesOfService;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\CashRegisterUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Stripe\Charge;
use Stripe\Stripe;
use Yajra\DataTables\Facades\DataTables;
use App\Events\SellCreatedOrModified;

use Illuminate\Support\Facades\Auth;

use Spatie\Activitylog\Models\Activity;

//{{--  personnalize custom code 25042024-MOBILEMONEY -- 25042024}}
use App\MobilemoneyPayLine;
//----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////


//{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
//attache les informations des devises liées à la location
use App\Currency;
//----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;



use Illuminate\Routing\Controller;

use App\Unit;


class UserController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $contactUtil;

    protected $productUtil;

    protected $businessUtil;

    protected $transactionUtil;

    protected $cashRegisterUtil;

    protected $moduleUtil;

    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(
        ContactUtil $contactUtil,
        ProductUtil $productUtil,
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        CashRegisterUtil $cashRegisterUtil,
        ModuleUtil $moduleUtil,
        NotificationUtil $notificationUtil
    ) {
        $this->contactUtil = $contactUtil;
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moduleUtil = $moduleUtil;
        $this->notificationUtil = $notificationUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash',
            'amount' => 0,
            'note' => '',
            'card_transaction_number' => '',
            'card_number' => '',
            'card_type' => '',
            'card_holder_name' => '',
            'card_month' => '',
            'card_year' => '',
            'card_security' => '',
            'cheque_number' => '',
            'bank_account_number' => '',
            'is_return' => 0,
            'transaction_no' => '',
        ];

        $this->shipping_status_colors = [
            'ordered' => 'bg-yellow',
            'packed' => 'bg-info',
            'shipped' => 'bg-navy',
            'delivered' => 'bg-green',
            'cancelled' => 'bg-red',
        ];
    }


    public function loginOfflineVersion(Request $request)
    {
        $credentials = $request->only('email', 'username', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            //je fait appel au route qui initie la base de donnée offline
            $this->index(Auth::User());
            $output = [
                'success' => true,
                'msg' => "Vous etes connecté avec succes",
                'data' => Auth::User()
            ];
            return $output;
        }

        $output = [
            'success' => 0,
            'msg' => 'Utilisateur non trouvé'
        ];
        return response()->json($output, 401);
    }






    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');


        $headers = [
            'Content-Type' => 'application/json',
            'H360AI-ADMIN-EMAIL' => env('H360AI_ADMIN_EMAIL'),
            'H360AI-ADMIN-PASS' => env('H360AI_ADMIN_PASS'),
            'H360AI-USER-SIGNATURE' => env('H360AI_USER_SIGNATURE'),
            'H360AI-CHATBOT-SIGNATURE' => env('H360AI_CHATBOT_SIGNATURE'),
        ];
        $postdata = [
            "business_id" => $business_id,
            "business_name" => request()->session()->get('business.name'),
            "user_id" => request()->session()->get('user.id'),
            "user_language" => request()->session()->get('user.language'),
            "user_surname" => request()->session()->get('user.surname'),
            "user_first_name" => request()->session()->get('user.first_name'),
            "user_last_name" => request()->session()->get('user.last_name'),

        ];
        

        if (empty(request()->session()->get('business'))) {
            //si aucune connexion active on ne fait pas les histoire de H360
            die();
        }


        //dd($headers);
        $postdata = ["username" => "USER_H360_" . $business_id];
        $client = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => false
        ]);
        $_response = $client->get(env('H360AI_ADMIN_API_URL') . '/h360pos/users', [
            'headers' => $headers,
            'body' => json_encode($postdata)
        ]);
        dd(json_decode($_response->getBody()->getContents()));


        $_sms_users = json_decode($_response->getBody()->getContents());
        $sms_users = collect($_sms_users); //convertir en collection afin de faire les recherche dedans

        if (empty($sms_users)) {
            dd("impossible d'utiliser les sms car vous n'avez pas des representant");
        }

        dd(111);

        /*
        if (!(auth()->user()->can('superadmin'))) {
            abort(403, 'Unauthorized action.');
        }
        */
        $user = auth()->user();
        $business = $user->business;

        //je verifie si l'utilisateur existe dans l'API H360GPT (user_1)
        //je passe en argument les logins de l'API

        dd($user->business);

        dd(123456);



        /*
        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
        $bl_attributes = $business_locations['attributes'];
        $business_locations = $business_locations['locations'];
        */

        $locations = BusinessLocation::where('business_locations.business_id', $business_id)
            ->leftjoin(
                'invoice_schemes as ic',
                'business_locations.invoice_scheme_id',
                '=',
                'ic.id'
            )
            ->leftjoin(
                'invoice_layouts as il',
                'business_locations.invoice_layout_id',
                '=',
                'il.id'
            )
            ->leftjoin(
                'invoice_layouts as sil',
                'business_locations.sale_invoice_layout_id',
                '=',
                'sil.id'
            )
            ->leftjoin(
                'selling_price_groups as spg',
                'business_locations.selling_price_group_id',
                '=',
                'spg.id'
            )
            ->select([
                'business_locations.name',
                'location_id',
                'landmark',
                'city',
                'zip_code',
                'state',
                'country',
                'business_locations.id',
                'spg.name as price_group',
                'ic.name as invoice_scheme',
                'il.id as invoice_layout_id',
                'il.name as invoice_layout_name',
                'sil.name as sale_invoice_layout',
                'business_locations.is_active',
            ]);
        /*
        $permitted_locations = $user->permitted_locations();
        if ($permitted_locations != 'all') {
            $locations->whereIn('business_locations.id', $permitted_locations);
        }
        */
        $businesslocations = $locations->get()->toArray();




        $business_details = $this->businessUtil->getDetails($business_id);

        /************ on recupere tous les contacts de l'entreprise ****************/
        $contacts = Contact::where('contacts.business_id', $business_id)
            ->leftjoin('customer_groups as cg', 'cg.id', '=', 'contacts.customer_group_id')
            ->active();
        $contacts->select(
            'contacts.id',
            DB::raw("IF(contacts.contact_id IS NULL OR contacts.contact_id='', contacts.name, CONCAT(contacts.name, ' (', contacts.contact_id, ')')) AS text"),
            'mobile',
            'address_line_1',
            'address_line_2',
            'city',
            'state',
            'country',
            'zip_code',
            'shipping_address',
            'pay_term_number',
            'pay_term_type',
            'balance',
            'supplier_business_name',
            'cg.amount as discount_percent',
            'cg.price_calculation_type',
            'cg.selling_price_group_id',
            'shipping_custom_field_details',
            'is_export',
            'export_custom_field_1',
            'export_custom_field_2',
            'export_custom_field_3',
            'export_custom_field_4',
            'export_custom_field_5',
            'export_custom_field_6',
            'export_custom_field_6 as due',
        );

        $contacts->addSelect('total_rp');
        $contacts = $contacts->get()->toArray();



        foreach ($contacts as $key => $value) {
            $due = $this->transactionUtil->getContactDue($value['id'], $business_id);
            //$due = $due != 0 ? $this->transactionUtil->num_f($due, true) : ''; necessite une session
            $contacts[$key]['due'] = $due;

            $customer = Contact::find($value['id']);
            $contacts[$key] = array_merge($contacts[$key], $customer->toArray());
        }

        /***************************************************************************/

        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action([\App\Http\Controllers\HomeController::class, 'index']));
        } elseif (!$this->moduleUtil->isQuotaAvailable('invoices', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('invoices', $business_id, action([\App\Http\Controllers\SellPosController::class, 'index']));
        }


        $type = !empty(request()->input('sale_type')) ? request()->input('sale_type') : 'sell';








        $sells = Transaction::leftJoin('tax_rates as tr', 'transactions.tax_id', '=', 'tr.id')
            ->leftJoin('contacts as c', 'transactions.contact_id', '=', 'c.id')
            ->where('transactions.business_id', $business_id)
            ->with(['payment_lines'])
            ->select(
                'c.name as contact_name',
                'c.supplier_business_name',
                'c.tax_number',
                'transactions.ref_no',
                'transactions.invoice_no',
                'transactions.transaction_date',
                'transactions.total_before_tax',
                'transactions.tax_id',
                'transactions.tax_amount',
                'transactions.id',
                'transactions.type',
                'transactions.discount_type',
                'transactions.discount_amount'
            );
        if ($type == 'sell') {
            $sells->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->where(function ($query) {
                    $query->whereHas('sell_lines', function ($q) {
                        $q->whereNotNull('transaction_sell_lines.tax_id');
                    })->orWhereNotNull('transactions.tax_id');
                })
                ->with([
                    'sell_lines' => function ($q) {
                        $q->whereNotNull('transaction_sell_lines.tax_id');
                    },
                    'sell_lines.line_tax'
                ]);
        }
        if ($type == 'purchase') {
            $sells->where('transactions.type', 'purchase')
                ->where('transactions.status', 'received')
                ->where(function ($query) {
                    $query->whereHas('purchase_lines', function ($q) {
                        $q->whereNotNull('purchase_lines.tax_id');
                    })->orWhereNotNull('transactions.tax_id');
                })
                ->with([
                    'purchase_lines' => function ($q) {
                        $q->whereNotNull('purchase_lines.tax_id');
                    },
                    'purchase_lines.line_tax'
                ]);
        }

        if ($type == 'expense') {
            $sells->where('transactions.type', 'expense')
                ->whereNotNull('transactions.tax_id');
        }
        /*
            $permitted_locations = $user->permitted_locations();
            if ($permitted_locations != 'all') {
                $sells->whereIn('transactions.location_id', $permitted_locations);
            }
            */

        if (request()->has('location_id')) {
            $location_id = request()->get('location_id');
            if (!empty($location_id)) {
                $sells->where('transactions.location_id', $location_id);
            }
        }

        if (request()->has('contact_id')) {
            $contact_id = request()->get('contact_id');
            if (!empty($contact_id)) {
                $sells->where('transactions.contact_id', $contact_id);
            }
        }

        if (!empty(request()->start_date) && !empty(request()->end_date)) {
            $start = request()->start_date;
            $end = request()->end_date;
            $sells->whereDate('transactions.transaction_date', '>=', $start)
                ->whereDate('transactions.transaction_date', '<=', $end);
        }
        $_sells = $sells->get();
        //dd($_sells);

        $selles_array = [];
        foreach ($_sells as $key => $_sell) {
            $taxes = TaxRate::where('business_id', $business_id)
                ->pluck('name', 'id');
            $query = Transaction::where('business_id', $business_id)
                ->where('id', $_sell->id)
                ->with([
                    'contact',
                    'delivery_person_user',
                    'sell_lines' => function ($q) {
                        $q->whereNull('parent_sell_line_id');
                    },
                    'sell_lines.product',
                    'sell_lines.product.unit',
                    'sell_lines.product.second_unit',
                    'sell_lines.variations',
                    'sell_lines.variations.product_variation',
                    'payment_lines',
                    'sell_lines.modifiers',
                    'sell_lines.lot_details',
                    'tax',
                    'sell_lines.sub_unit',
                    'table',
                    'service_staff',
                    'sell_lines.service_staff',
                    'types_of_service',
                    'sell_lines.warranties',
                    'media'
                ]);

            if (!$user->can('sell.view') && !$user->can('direct_sell.access') && $user->can('view_own_sell_only')) {
                $query->where('transactions.created_by', request()->session()->get('user.id'));
            }

            $sell = $query->firstOrFail();


            $activities = Activity::forSubject($sell)
                ->with(['causer', 'subject'])
                ->latest()
                ->get();

            $line_taxes = [];
            foreach ($sell->sell_lines as $key => $value) {
                if (!empty($value->sub_unit_id)) {
                    $formated_sell_line = $this->transactionUtil->recalculateSellLineTotals($business_id, $value);
                    $sell->sell_lines[$key] = $formated_sell_line;
                }

                if (!empty($taxes[$value->tax_id])) {
                    if (isset($line_taxes[$taxes[$value->tax_id]])) {
                        $line_taxes[$taxes[$value->tax_id]] += ($value->item_tax * $value->quantity);
                    } else {
                        $line_taxes[$taxes[$value->tax_id]] = ($value->item_tax * $value->quantity);
                    }
                }
            }

            $payment_types = $this->transactionUtil->payment_types($sell->location_id, true);
            $order_taxes = [];
            if (!empty($sell->tax)) {
                if ($sell->tax->is_tax_group) {
                    $order_taxes = $this->transactionUtil->sumGroupTaxDetails($this->transactionUtil->groupTaxDetails($sell->tax, $sell->tax_amount));
                } else {
                    $order_taxes[$sell->tax->name] = $sell->tax_amount;
                }
            }

            $business_details = $this->businessUtil->getDetails($business_id);
            $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);
            $shipping_statuses = $this->transactionUtil->shipping_statuses();
            $shipping_status_colors = $this->shipping_status_colors;
            $common_settings = session()->get('business.common_settings');
            $is_warranty_enabled = !empty($common_settings['enable_product_warranty']) ? true : false;

            $statuses = Transaction::sell_statuses();

            if ($sell->type == 'sales_order') {
                $sales_order_statuses = Transaction::sales_order_statuses(true);
                $statuses = array_merge($statuses, $sales_order_statuses);
            }
            $status_color_in_activity = Transaction::sales_order_statuses();
            $sales_orders = $sell->salesOrders();

            $selles_array[$_sell->invoice_no] = compact(
                'taxes',
                'sell',
                'payment_types',
                'order_taxes',
                'pos_settings',
                'shipping_statuses',
                'shipping_status_colors',
                'is_warranty_enabled',
                'activities',
                'statuses',
                'status_color_in_activity',
                'sales_orders',
                'line_taxes'
            );
        }




        return view('h360ai.general')
            ->with(compact(
                'business_details',
                'selles_array',
            ));
    }

    public function doc()
    {
        return view('h360ai.documentation');

    }
}
