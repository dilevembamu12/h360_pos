<?php

namespace App\Listeners;

use App\Events\ProductsCreatedOrModified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Storage;


use App\Brands;
use App\BusinessLocation;
use App\CashRegister;
use App\Category;
use App\Charts\CommonChart;
use App\Contact;
use App\CustomerGroup;
use App\ExpenseCategory;
use App\Product;
use App\PurchaseLine;
use App\Restaurant\ResTable;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\TransactionSellLinesPurchaseLines;
use App\Unit;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\VariationLocationDetails;//CUSTOM
use Datatables;
use DB;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use Spatie\Activitylog\Models\Activity;

/*
je dois mettre l'event dans les endroits ci apres
- update/delete/add product
- sell 
- purchase
*/

class ProductListener
{
    /**
     * All Utils instance.
     */
    protected $transactionUtil;

    protected $productUtil;

    protected $moduleUtil;

    protected $businessUtil;




    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\ProductsCreatedOrModified  $event
     * @return void
     */
    public function handle(ProductsCreatedOrModified $event)
    {


        $business_id = auth()->user()->business_id;// $request->session()->get('user.business_id');

        $filters = [];
        $filters['not_for_selling'] = 0;
        $filters['show_manufacturing_data'] = 0;
        $for = 'datatables';

        $products = $this->productUtil->getProductStockDetails($business_id, $filters, $for)->where('p.id', $event->product->id)->get();
        $totrain_locations = [];
        $totrain_users = [];
        foreach ($products as $key => $product) {
            $totrain_locations[] = $product->location_id;
        }


        $users = User::where('business_id', $business_id)
            ->user()
            ->where('is_cmmsn_agnt', 0)
            ->where('allow_login', 1)
            ->get();
        foreach ($users as $key => $totrain) {
            $pl = $totrain->permitted_locations();

            if ($pl == 'all') {
                $totrain_users[] = $totrain;
            } else {
                foreach ($totrain_locations as $key2 => $value) {
                    if (in_array($value, $pl)) {
                        $totrain_users[] = $totrain;
                        break;
                    }
                }

            }
        }

        foreach ($totrain_users as $key => $totrain_user) {
            /*
            //seul les utilisateurs qui ont l'autorisation qui peuvent entrainer le chatbot
            if ($totrain_user->can('stock_report.view')){
                $this->setMaterial($event->product->id, $totrain_user);
            }
            */
            $this->setMaterial($event->product->id, $totrain_user);
            
        }
        //dd($totrain_users);
    }

    protected function setMaterial($product_id, $user)
    {
        try {//j'ai instauré le try cach puisque quand on ajoute le produit ca crée une erreur 
            //==>[2025-03-22 19:43:44] live.ERROR: Call to a member function setAlgin() on null {"userId":170,"exception":"[object] (Error(code: 0): Call to a member function setAlgin() on null at /www/wwwroot/BUSINESS/ENTREPRISE/H360/INTERNE/STACK/stackpos.h360.cd/app/Listeners/ProductListener.php:171)

            //on ne traine que l'utilisateur qui a droit de voir le rapport de stock
            if ($user->can('stock_report.view')) {
                $business_id = $user->business_id;// $request->session()->get('user.business_id');

                $selling_price_groups = SellingPriceGroup::where('business_id', $business_id)
                    ->get();
                $allowed_selling_price_group = false;
                foreach ($selling_price_groups as $selling_price_group) {
                    if ($user->can('selling_price_group.' . $selling_price_group->id)) {
                        $allowed_selling_price_group = true;
                        break;
                    }
                }
                if ($this->moduleUtil->isModuleInstalled('Manufacturing') && ($user->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module'))) {
                    $show_manufacturing_data = 1;
                } else {
                    $show_manufacturing_data = 0;
                }

                $filters = [];
                $filters['not_for_selling'] = isset($filters['not_for_selling']) && $filters['not_for_selling'] == 'true' ? 1 : 0;
                $filters['show_manufacturing_data'] = $show_manufacturing_data;
                $for = 'datatables';

                $products = $this->productUtil->getProductStockDetails($business_id, $filters, $for)->where('p.id', $product_id)->get();




                //dd($products);
                $data = [];
                $t = null;
                foreach ($products as $key => $product) {
                    $stock = $product->stock ? $product->stock : 0;
                    $unit_selling_price = (float) $product->group_price > 0 ? $product->group_price : $product->unit_price;
                    $stock_price_by_sp = $stock * $unit_selling_price;
                    $potential_profit = (float) $stock_price_by_sp - (float) $product->stock_price;

                    $product_locations[] =
                        $data = [
                            "quantité totale de stock vendu" => (empty($product->total_sold)) ? "jamais vendu " : $product->total_sold . " " . $product->unit,
                            "quantité totale de stock transferé" => (empty($product->total_transfered)) ? "jamais transferé " : $product->total_transfered . " " . $product->unit,
                            "quantité totale de stock endommagé" => (empty($product->total_adjusted)) ? "jamais endommagé " : $product->total_adjusted . " " . $product->unit,
                            "valeur du cout d'achat de tout le stock" => $this->transactionUtil->num_f($product->stock_price, true),//mettre la devise ici
                            "Valeur du stock si tout le stock est vendu" => $this->transactionUtil->num_f($product->stock, true),//mettre la devise ici
                            "sku ou code produit" => $product->sku,
                            "nom du product" => $product->product,
                            "type du produit" => $product->type,
                            "quantité d'alert de rupture de stock" => $product->type,
                            "Identifiant(ID) du produit" => $product->id,
                            "Unité du produit" => $product->unit,
                            "Nature du produit" => ($product->enable_stock == 1) ? "produit est physique" : "le produit est un service",
                            "Prix unitaire" => $this->transactionUtil->num_f($product->unit_price, true),
                            "variation du produit" => ($product->product_variation == "DUMMY") ? "le produit n'a pas de variation" : "C'est un produit variable en \"" . $product->product_variation . "\"",
                            "Nom de la variation" => ($product->product_variation == "DUMMY") ? "le produit n'a pas de variation" : $product->product_variation . " : \"" . $product->variation_name . "\"",
                            "Emplacement du produit" => (empty($product->location_name)) ? "le produit pas encore affecté" : $product->location_name,
                            "Identifiant(ID) de l'emplacement du produit" => (empty($product->location_id)) ? "le produit pas encore affecté" : $product->location_id,
                            "Identifiant(ID) de la variation" => $product->variation_id,
                            "Catégorie" => (empty($product->category_name)) ? "le produit n'appartient à aucune catégorie" : $product->category_name,
                            //"box_unit_id" => 1,
                            "Marque" => (empty($product->brand_name)) ? "le produit n'appartient à aucune marque" : $product->brand_name,
                            "Marge bénéficiaire potentielle" => $this->transactionUtil->num_f($potential_profit, true),
                            "product_custom_field1" => $product->product_custom_field1,
                            "product_custom_field2" => $product->product_custom_field2,
                            "product_custom_field3" => $product->product_custom_field3,
                            "product_custom_field4" => $product->product_custom_field4,
                            //"subunits" => "W10=",
                            //"total_mfg_stock" => "0.0000"
                        ];
                    if (empty($t)) {
                        $columns = array_keys($data);
                        $rows = [array_values($data)];
                        $t = new TextTable($columns, $rows);

                    } else {
                        $rows = array_values($data);
                        $t->addData([$key => $rows]);
                    }
                }
                $t->setAlgin(['L', 'C', 'R']);
                $content = strtolower("##rapport de stock pour le produit \"" . $product->product . "\"\n\n##Role:Explorer ce contenu quand l'utilisateur demande les etats de stock, ou toute information detaillée liée au produit en specifiant son identifiant(ID) ou le nom du produit \n\n" . $t->render());
            

            }else{
                $content = strtolower("desolé ".$user->surname." ".$user->first_name." ".$user->last_name." ".$user->username.", conformément à la politique interne de ".$user->business->name.",l'accès à ces informations est restreint. Pour toute question ou demande de clarification à ce sujet, je vous invite à vous adresser à votre administrateur principal.");
            
            }

            //dd($t->render());

            /*
            $columns = array_keys($data);
            dd($columns);
            $rows = [array_values($data)];
            $t = new TextTable($columns, $rows);
            */

            //dd(12345);
            $root_path = $_SERVER['DOCUMENT_ROOT'] . "/../storage/app/h360ai/h360gpt";
            $path_level1 = $root_path . "/business_" . $user->business_id;
            if (!is_dir($path_level1)) {//si le dossier n'existe pas on le crée
                mkdir($path_level1, 0777, true);
            }
            ;

            $path_level2 = $path_level1 . "/bot_" . $user->business_id . "_" . $user->id;
            if (!is_dir($path_level2)) {//si le dossier n'existe pas on le crée
                mkdir($path_level2, 0777, true);
            }
            ;

            $file_path = $path_level2 . "/stock_product_" . $product->product_id . ".txt";
            //dd(111);
            $already_content = file_exists($file_path) ? md5_file($file_path) : '';
            if (md5($content) != $already_content) {
                $fp = fopen($file_path, "wb");
                fwrite($fp, $content);
                fclose($fp);
                //dd("pas existant");
            }
            //dd("existant");


        } catch (\Throwable $th) {
            //throw $th;
        }


        //Storage::disk('local')->put('example.txt', 'Contents');
        //dd(111);
    }
}


class TextTable
{
    /** @var int The source path */
    public $maxlen = 50;
    /** @var array The source path */
    private $data = array();
    /** @var array The source path */
    private $header = array();
    /** @var array The source path */
    private $len = array();
    /** @var array The source path */
    private $align = array(
        'name' => 'L',
        'type' => 'C'
    );

    /**
     * @param array $header  The header array [key => label, ...]
     * @param array $content Content
     * @param array $align   Alignment optios [key => L|R|C, ...]
     */
    public function __construct($header = null, $content = array(), $align = false)
    {
        if ($header) {
            $this->header = $header;
        } elseif ($content) {
            foreach ($content[0] as $key => $value)
                $this->header[$key] = $key;
        }

        foreach ($this->header as $key => $label) {
            $this->len[$key] = strlen($label);
        }

        if (is_array($align))
            $this->setAlgin($align);

        $this->addData($content);
    }

    /**
     * Overwrite the alignment array
     *
     * @param array $align   Alignment optios [key => L|R|C, ...]
     */
    public function setAlgin($align)
    {
        $this->align = $align;
    }

    /**
     * Add data to the table
     *
     * @param array $content Content
     */
    public function addData($content)
    {
        foreach ($content as &$row) {
            foreach ($this->header as $key => $value) {
                if (!isset($row[$key])) {
                    $row[$key] = '-';
                } elseif (strlen($row[$key]) > $this->maxlen) {
                    $this->len[$key] = $this->maxlen;
                    $row[$key] = substr($row[$key], 0, $this->maxlen - 3) . '...';
                } elseif (strlen($row[$key]) > $this->len[$key]) {
                    $this->len[$key] = strlen($row[$key]);
                }
            }
        }

        $this->data = $this->data + $content;
        return $this;
    }

    /**
     * Add a delimiter
     *
     * @return string
     */
    private function renderDelimiter()
    {
        $res = '|';
        foreach ($this->len as $key => $l)
            $res .= (isset($this->align[$key]) && ($this->align[$key] == 'C' || $this->align[$key] == 'L') ? ':' : ' ')
                . str_repeat('-', $l)
                . (isset($this->align[$key]) && ($this->align[$key] == 'C' || $this->align[$key] == 'R') ? ':' : ' ')
                . '|';
        return $res . "\r\n";
    }

    /**
     * Render a single row
     *
     * @param  array $row
     * @return string
     */
    private function renderRow($row)
    {
        $res = '|';
        foreach ($this->len as $key => $l) {
            $res .= ' ' . $row[$key] . ($l > strlen($row[$key]) ? str_repeat(' ', $l - strlen($row[$key])) : '') . ' |';
        }

        return $res . "\r\n";
    }

    /**
     * Render the table
     *
     * @param  array  $content Additional table content
     * @return string
     */
    public function render($content = array())
    {
        $this->addData($content);

        $res = $this->renderRow($this->header)
            . $this->renderDelimiter();
        foreach ($this->data as $row)
            $res .= $this->renderRow($row);

        return $res;
    }
}