<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;

use App\Business;
use App\BusinessLocation;
use App\Contact;

use Yajra\DataTables\Facades\DataTables;
use App\Events\ContactCreatedOrModified;



use App\Account;
use App\Brands;
use App\Category;
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
use App\Utils\ProductUtil;
use App\Variation;
use App\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Stripe\Charge;
use Stripe\Stripe;
use App\Events\SellCreatedOrModified;


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


use Illuminate\Support\Facades\Artisan;

class CouchDbSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:CouchDbSync {--business_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronisation automatique de la base de données offline et online';


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

    protected $db_url;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(
        Util $commonUtil,
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

        $this->db_url = env("COUCHDB_URL");
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');
        //try {
        $businesses = Business::all();

        //je recupere tous les db deja crée
        $_all_dbs=$this->getAllCouchdb();
        dd($_all_dbs);

            foreach ($businesses as $business_key => $business) {
                //if($business_key!=11){continue;}
                $business_id = $business->id;
                //je crée le offline de l'entreprse si jamais il n'existe pas
                //il n'existe pas on le crée
                if(!in_array("h360_$business_id",$_all_dbs)){
                    $db = $this->createCouchdb($business_id);
                }
                
            }

            


            foreach ($businesses as $business_key => $business) {
                //if($business_key!=11){continue;}
                $business_id = $business->id;
                //puis on puller si il ya en a 
                $pulldb = $this->OnToOff($business_id);
            }
            foreach ($businesses as $business_key => $business) {
                //if($business_key!=11){continue;}
                $business_id = $business->id;
                //$pushdb = $this->OffToOn($business_id);
                //if($pushdb==12345){dd($business_key);}
            }
            dd(count($businesses));
        /*
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            exit($e->getMessage());
        }
        */


        //$this->OnToOff();
        //$this->OffToOn();
    }

    //données venant de la base de donnée online vers offline
    
    protected function OnToOff($business_id)
    {
        $users = User::where('business_id', $business_id)
            //->user()
            ->where('is_cmmsn_agnt', 0)
            //->with(['role'])
            /*
            ->select([
                'id',
                'username',
                DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                'email',
                'allow_login',
            ])
            */
            ->get();

        foreach ($users as $user_key => $user) {

            $user_id = $user->id;
            $user->load('roles.permissions');

            
            $locations = BusinessLocation::where('business_locations.business_id', $business_id)
            ->where('business_locations.is_active', 1)
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
            
                
            $permitted_locations = $user->permitted_locations();
            if ($permitted_locations != 'all') {
                $locations->whereIn('business_locations.id', $permitted_locations);
            }
            $businesslocations = $locations->get()->toArray();
            //dd($businesslocations);
            
            $_business_locations = $this->BusinessLocation_forDropdown($user,$permitted_locations, $business_id, false, true);
            //dd(["businesslocations"=>$businesslocations,"_business_locations"=>$_business_locations]);

            foreach ($businesslocations as $businesslocations_key => $businesslocation) {
                //dd($businesslocation);

                /********** CONFIG  */
                //les données de configurations 
                $invoice_layout_id = !empty($invoice_layout_id) ? $invoice_layout_id : $businesslocation["invoice_layout_id"];
                $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $invoice_layout_id);
                $businesslocations[$businesslocations_key]["invoice_layout"] = $invoice_layout->toArray();
                //dd($invoice_layout);
                /************************************************************************** */


                
                $location_id = $businesslocation['id'];

                /*
            $products = Variation::join('products as p', 'variations.product_id', '=', 'p.id')
                ->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                ->leftjoin(
                    'variation_location_details AS VLD',
                    function ($join) use ($location_id) {
                        $join->on('variations.id', '=', 'VLD.variation_id');

                        //Include Location
                        if (!empty($location_id)) {
                            $join->where(function ($query) use ($location_id) {
                                $query->where('VLD.location_id', '=', $location_id);
                                //Check null to show products even if no quantity is available in a location.
                                //TODO: Maybe add a settings to show product not available at a location or not.
                                $query->orWhereNull('VLD.location_id');
                            });
                        }
                    }
                )
                ->where('p.business_id', $business_id)
                ->where('p.type', '!=', 'modifier')
                ->where('p.is_inactive', 0)
                ->where('p.not_for_selling', 0)
                //Hide products not available in the selected location
                ->where(function ($q) use ($location_id) {
                    $q->where('pl.location_id', $location_id);
                });




            $products = $products->select(
                'p.id as product_id',
                'p.name',
                'p.type',
                'p.enable_stock',
                'p.image as product_image',
                'variations.id as variation_id',
                'variations.name as variation',
                'VLD.qty_available',
                'variations.default_sell_price as selling_price',
                'variations.sub_sku'
            )
                ->with(['media', 'group_prices'])
                ->orderBy('p.name', 'asc')
                ->get();
                $products=$products->filter(function($value,$key){
                    //on recupere seulement les produits ayant du stock , en prenant soin de prendre en compte les produit etant comme des services
                    if($value->enable_stock==0 || ($value->enable_stock==1 && $value->qty_available>1)){
                        return $value;
                    }
                });

            */
                $check_qty = true;
                $query = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
                    ->join('product_variations AS pv', 'variations.product_variation_id', '=', 'pv.id')
                    ->leftjoin('variation_location_details AS vld', 'variations.id', '=', 'vld.variation_id')
                    ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                    ->leftjoin('units as u', 'p.secondary_unit_id', '=', 'u.id')
                    ->leftjoin('brands', function ($join) {
                        $join->on('p.brand_id', '=', 'brands.id')
                            ->whereNull('brands.deleted_at');
                    })
                    ->where('p.is_inactive', 0)
                    ->where('p.business_id', $business_id);

                //Add condition for check of quantity. (if stock is not enabled or qty_available > 0)
                if ($check_qty) {
                    $query->where(function ($query) {
                        $query->where('p.enable_stock', '!=', 1)
                            ->orWhere('vld.qty_available', '>', 0);
                    });
                }

                if (!empty($location_id) && $check_qty) {
                    //Check for enable stock, if enabled check for location id.
                    $query->where(function ($query) use ($location_id) {
                        $query->where('p.enable_stock', '!=', 1)
                            ->orWhere('vld.location_id', $location_id);
                    });
                }

                //only single product / variable product
                $query->whereIn('p.type', ['single']);

                $products = $query->select(
                    DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, 
                    ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                    'p.id as product_id',
                    'p.brand_id',
                    'p.category_id',
                    'p.tax as tax_id',
                    'p.enable_stock',
                    'p.enable_sr_no',
                    'p.type as product_type',
                    'p.name as product_actual_name',
                    'p.warranty_id',
                    'p.image',
                    'p.product_custom_field1',
                    'p.product_custom_field2',
                    'p.product_custom_field3',
                    'p.product_custom_field4',
                    'p.product_custom_field5',
                    'p.product_custom_field6',
                    'p.product_custom_field7',
                    'p.product_custom_field8',
                    'p.product_custom_field9',
                    'p.product_custom_field10',
                    'p.product_custom_field11',
                    'p.product_custom_field12',
                    'p.product_custom_field13',
                    'p.product_custom_field14',
                    'p.product_custom_field15',
                    'p.product_custom_field16',
                    'p.product_custom_field17',
                    'p.product_custom_field18',
                    'p.product_custom_field19',
                    'p.product_custom_field20',
                    'pv.name as product_variation_name',
                    'pv.is_dummy as is_dummy',
                    'variations.name as variation_name',
                    'variations.sub_sku',
                    'p.barcode_type',
                    'vld.qty_available',
                    'variations.default_sell_price',
                    'variations.sell_price_inc_tax',
                    'variations.id as variation_id',
                    'variations.combo_variations',  //Used in combo products
                    'units.short_name as unit',
                    'units.id as unit_id',
                    'units.allow_decimal as unit_allow_decimal',
                    'p.sub_unit_ids',
                    'u.short_name as second_unit',
                    'brands.name as brand',
                    DB::raw('(SELECT purchase_price_inc_tax FROM purchase_lines WHERE 
                        variation_id=variations.id ORDER BY id DESC LIMIT 1) as last_purchased_price')
                )->get();

                //dd($products);

                $products->filter(function ($product, $key) use ($check_qty, $location_id, $business_id) {
                    //on recupere seulement les produits ayant du stock , en prenant soin de prendre en compte les produit etant comme des services

                    if ($product->product_type == 'combo') {
                        if ($check_qty) {
                            $product->qty_available = $this->calculateComboQuantity($location_id, $product->combo_variations);
                        }
                        $product->combo_products = $this->calculateComboDetails($location_id, $product->combo_variations);
                    }

                    try {
                        $unit = Unit::where('business_id', $business_id)
                            ->with(['sub_units'])
                            ->findOrFail($product->unit_id);
                        //dd($unit);

                        $product['unit'] = $unit;

                        $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit_id, false, $product->product_id);
                        $product['sub_units'] = $sub_units;
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    $products[$key] = $product;
                });

                //dd($_business_locations);
                /*
                if($location_id==38){
                    dd("error ");
                    dd($_business_locations);
                    dd($businesslocation);
                    return 12345;
                }
                */

                $businesslocations[$businesslocations_key]['products'] = $products->toArray();
                $businesslocations[$businesslocations_key]['attributes'] = $_business_locations['attributes'][$location_id];

                $location_details = BusinessLocation::find($location_id);
                $businesslocations[$businesslocations_key] = array_merge($businesslocations[$businesslocations_key], $location_details->toArray());
                //dd($businesslocations);


                $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');

                $allowed_group_prices = [];
                foreach ($price_groups as $key => $value) {
                    if ($user->can('selling_price_group.' . $key)) {
                        $allowed_group_prices[$key] = $value;
                    }
                }

                $show_prices = !empty($pos_settings['show_pricing_on_product_sugesstion']);
            }
            //dd($businesslocations);
            $business_details = $this->businessUtil->getDetails($business_id);
            //dd($business_details);


            //on recuperer le role et permissions


            //$user = User::where('id', $user_id)->with(['media'])->first();
            
            /*********** recuperation autres informations utiles */
            $register_details = $this->cashRegisterUtil->getCurrentCashRegister($user_id);
            $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
            //$taxes = TaxRate::forBusinessDropdown($business_id, true, true);

            $taxes = TaxRate::where('business_id', $business_id)
                //->select(['id', 'name', 'amount'])
                ->get()->toArray();
            /*
            $taxes->filter(function ($tax, $key)  {
            //on met toutes les dettes du client
            $sub_taxes = $tax->sub_taxes;
            $sum = $tax->sub_taxes->sum('amount');

            $details = [];
            foreach ($sub_taxes as $sub_tax) {
                $details[] = [
                    'id' => $sub_tax->id,
                    'name' => $sub_tax->name,
                    'amount' => $sub_tax->amount,
                    'calculated_tax' => ($amount / $sum) * $sub_tax->amount,
                ];
            }

            $tax[$key]['sub_taxes']=$details;

        });
        */
            //dd($taxes);

            /*
        $sub_taxes = $tax->sub_taxes;

            $sum = $tax->sub_taxes->sum('amount');

            $details = [];
            foreach ($sub_taxes as $sub_tax) {
                $details[] = [
                    'id' => $sub_tax->id,
                    'name' => $sub_tax->name,
                    'amount' => $sub_tax->amount,
                    'calculated_tax' => ($amount / $sum) * $sub_tax->amount,
                ];
            }

            return $details;
            */


            //dd($taxes);
            $payment_lines[] = [
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

            /*************************************************** */

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

            //dd($register_details);
            //$doc = ["business" => (array)request()->session()->get('business')->toArray(), "businesslocations" => $businesslocations];
            $doc = [
                "user" => $user->toArray(),
                "contacts" => $contacts,
                "register_details" => (!empty($register_details)) ? $register_details->toArray() : null,
                "walk_in_customer" => $walk_in_customer,
                "taxes" => $taxes,
                "payment_lines" => $payment_lines,
                "business" => $business_details->toArray(),
                "businesslocations" => $businesslocations,
                "sells" => ["to" => [], "log" => []]
            ];

            //si il y a une revision qui existe
            //mais avant tout je verifie si la clé de revisiion exits pour savoir si la base de donnée est vide ou soit n'a pas été changé 
            //dd($user->couchdb_rev);
            //si ca n'existe pas enccore
            $client = new Client();
            $status = null;
            try {
                $res = $client->request('GET', "$this->db_url$business_id" . "/h360_$business_id" . "_$user_id", []);
                $status = $res->getStatusCode();
                //code...

            } catch (ClientException $e) {
                $status = $e->getResponse()->getStatusCode();
            } catch (RequestException $e) {
                $status = $e->getResponse()->getStatusCode();
            } catch (\Exception $e) {
                //dd(33);
            }

            //dd($user->couchdb_rev);
            //dd($status);
            if ($status == 200) {
                //si il ne le trouve pas 
                if (!empty($user->couchdb_rev)) {
                    $doc['_rev'] = $user->couchdb_rev;
                }
            }



            $doc["business"]['default_pos_settings'] = $this->businessUtil->defaultPosSettings();
            $couchdb_dbname = "h360_$business_id<-->h360_" . $business_id . "_$user_id";

            //$doc=json_encode($doc);

            //$doc = ['title'=>'This is a new doc'];
            $client = new Client();
            try {
                //si ca n'existe pas enccore
                $res = $client->request('PUT', "$this->db_url$business_id" . "/h360_$business_id" . "_$user_id", ['json' => $doc]);

                $body = json_decode((string) $res->getBody());

                $user = User::where('id', $user_id)->update(['couchdb_rev' => $body->rev]);
                $user = User::where('id', $user_id)->first();
                //dd($user);


            } catch (ClientException $e) {
                dd($e);
                //si ca existe deja on remplace
                $error['error'] = $e->getMessage();
                $error['request'] = $e->getRequest();
                if ($e->hasResponse()) {
                    // you can pass a specific status code to catch a particular error here I have catched 400 Bad Request. 
                    /*
              if ($e->getResponse()->getStatusCode() == '400'){
                 $error['response'] = $e->getResponse(); 
              }
              */
                    if ($e->getResponse()->getStatusCode() == '409') { //le document existe deja , on le modifie par sa clé _rev
                        //$doc['business']['name'] = (string) Str::uuid();


                        $user = User::where('id', $user_id)->first();
                        //dd($doc);
                        //$doc['_rev'] = $user->couchdb_rev;
                        $res = $client->request('PUT', "$this->db_url$business_id" . "/h360_$business_id" . "_$user_id", ['json' => $doc]);
                        $body = json_decode((string) $res->getBody());
                        $user->couchdb_rev = $body->rev;
                        $user->update();


                        //dd(789);
                        //$error['response'] = $e->getResponse();
                        //return $user;
                        //dd($user);
                    }
                }
                return $error;
            } catch (RequestException $e) {
                //dd(444);
                // dd(22);
            } catch (\Exception $e) {
                //dd(555);
                //dd(33);
            }
        }
    }

    //données venant de la base de donnée Offline vers Online
    protected function OffToOn(): void
    {
        
        //ici je recuper le contenu de la base de donnée 
        //je verifie que ce n'est pas null


        $users = User::where('business_id', $business_id)
            ->user()
            ->where('is_cmmsn_agnt', 0)
            /*
            ->select([
                'id',
                'username',
                DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                'email',
                'allow_login',
            ])
            */
            ->get();

        $client = new Client();
        foreach ($users as $user_key => $user) {
            //si ca n'existe pas enccore
            $user_id = $user->id;
            $status = null;
            try {
                $res = $client->request('GET', "$this->db_url$business_id" . "/h360_$business_id" . "_$user_id", []);
                $status = $res->getStatusCode();
                $body = json_decode((string) $res->getBody());
                //dd($body);
                //code...

            } catch (ClientException $e) {
                continue;
                $status = $e->getResponse()->getStatusCode();
            } catch (RequestException $e) {
                continue;
                $status = $e->getResponse()->getStatusCode();
            } catch (\Exception $e) {
                //dd(33);
            }


            //si il n'y pas des ventes à parser on continue
            if (!empty($body->sells->to)) {
                dd($body->sells->to);
            } else {
                continue;
            }
        }

    }




    protected function getAllCouchdb()
    {
        $url=str_replace('h360_','_all_dbs',$this->db_url);

        
        //$client = \Doctrine\CouchDB\CouchDBClient::create(array('dbname' => 'doctrine_example'));

        $client = new Client();
        $body = "";

        try {
            $response = $client->request('GET', "$url");
            $body = (string) $response->getBody();
            return json_decode($body,true);
            //dd(1221);
        } catch (ClientException $e) {
            //    dd(1);
            // Do some thing here...
        } catch (RequestException $e) {
            // Do some thing here...
        } catch (\Exception $e) {
            // Do some thing here...
        }
    }
    protected function createCouchdb($business_id)
    {
        //$client = \Doctrine\CouchDB\CouchDBClient::create(array('dbname' => 'doctrine_example'));

        $client = new Client();
        $doc = ['title' => 'This is a new doc'];
        $body = "";

        try {
            $response = $client->request('PUT', "$this->db_url$business_id");
            $body = (string) $response->getBody();
            return $body;
            //dd(1221);
        } catch (ClientException $e) {
            //    dd(1);
            // Do some thing here...
        } catch (RequestException $e) {
            // Do some thing here...
        } catch (\Exception $e) {
            // Do some thing here...
        }
    }


    protected  function BusinessLocation_forDropdown($user,$user_permitted_locations,$business_id, $show_all = false, $receipt_printer_type_attribute = false, $append_id = true, $check_permission = true)
    {
        $query = BusinessLocation::where('business_id', $business_id)->Active();

        if ($check_permission) {
            $permitted_locations = $user_permitted_locations;
            if ($permitted_locations != 'all') {
                $query->whereIn('id', $permitted_locations);
            }
        }



        if ($append_id) {
            $query->select(
                DB::raw("IF(location_id IS NULL OR location_id='', name, CONCAT(name, ' (', location_id, ')')) AS name"),
                'id',
                'receipt_printer_type',
                'selling_price_group_id',
                'default_payment_accounts',
                'invoice_scheme_id',
                'invoice_layout_id',
                'sale_invoice_scheme_id'
                //{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
                //attache les informations des devises liées à la location
                ,
                'second_currency',
                'second_currency_rate'
                //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////
            );
        }

        $result = $query->get();

        $locations = $result->pluck('name', 'id');

        
        $price_groups = $this->SellingPriceGroup_forDropdown($user,$business_id);
  
        if ($show_all) {
            $locations->prepend(__('report.all_locations'), '');
        }

        if ($receipt_printer_type_attribute) {

            $attributes = collect($result)->mapWithKeys(function ($item) use ($price_groups) {

                //dd($item);
                $default_payment_accounts = json_decode($item->default_payment_accounts, true);
                $default_payment_accounts['advance'] = [
                    'is_enabled' => 1,
                    'account' => null,
                ];


                //{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
                //attache les informations des devises liées à la location
                $currencies = Currency::all();
                //$currency = Currency::find($item->second_currency);
                
                //dd($currencies);
                $currency=null;
                $currency_symbol =null ;
                $currency_code = null;

                foreach ($currencies as $key => $value) {  
                    if($value->id==$item->second_currency){
                        $currency_symbol = $value->symbol;
                        $currency_code = $value->code;
                    }
                }

                //$currency=$currencies[2];
                //dd($currency);
                //$currency->getOriginal();
                //$currency->getOriginal();
                
                

                
                //dd($currency_code );
       
                //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////
                return [
                    $item->id => [
                        'data-receipt_printer_type' => $item->receipt_printer_type,
                        'data-default_price_group' => !empty($item->selling_price_group_id) && array_key_exists($item->selling_price_group_id, $price_groups) ? $item->selling_price_group_id : null,
                        'data-default_payment_accounts' => json_encode($default_payment_accounts),
                        'data-default_sale_invoice_scheme_id' => $item->sale_invoice_scheme_id,
                        'data-default_invoice_scheme_id' => $item->invoice_scheme_id,
                        'data-default_invoice_layout_id' => $item->invoice_layout_id,
                        //{{--  personnalize custom code 23032024-MULTIDEVISE030 -- 24032024}}
                        //attache les informations des devises liées à la location
                        'second_currency' => $item->second_currency,
                        'second_currency_rate' => $item->second_currency_rate,
                        'second_currency_code' => $currency_code  ,
                        'second_currency_symbol' => $currency_symbol,
                        //----------------------- END PERSONNALIZE CUSTOM CODE-----------------------------------//////
                    ],
                ];
            })->all();

            return ['locations' => $locations, 'attributes' => $attributes];
        } else {
            return $locations;
        }
    }

    protected  function SellingPriceGroup_forDropdown($user,$business_id, $with_default = true)
    {
        $price_groups = SellingPriceGroup::where('business_id', $business_id)
                                    ->active()
                                    ->get();

        $dropdown = [];

        
        try {
            if ($with_default && in_array('access_default_selling_price',array_column($user->roles[0]->permissions->toArray(),'name')) ) {
                $dropdown[0] = __('lang_v1.default_selling_price');
            }
            
        } catch (\Throwable $th) {
            //throw $th;
        }

        foreach ($price_groups as $price_group) {
            try {
                if ($with_default && in_array('selling_price_group.'.$price_group->id,array_column($user->roles[0]->permissions->toArray(),'name')) ) {
                    $dropdown[$price_group->id] = $price_group->name;
                }
                
            } catch (\Throwable $th) {
                continue;
            }

        }

        return $dropdown;
    }

}
