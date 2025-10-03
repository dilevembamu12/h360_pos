<?php

namespace Modules\H360Copilot\Http\Controllers;

use App\User;
use App\Contact;
use App\Business;
use App\Currency;
use App\CashRegister;
use App\Product;
use App\Transaction;
use App\Utils\CashRegisterUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ApiController extends Controller
{
    /**
     * Sets up the business session so that the application's Utils work correctly.
     *
     * @param int $business_id
     * @param int $user_id
     * @param int|null $location_id
     * @return void
     */
    private function setupBusinessSession($business_id, $user_id, $location_id = null)
    {
        $business = Business::findOrFail($business_id);
        $user = User::findOrFail($user_id);
        
        $business->currency_symbol_placement = 'after';
        $currency = Currency::find($business->currency_id);

        session([
            'business' => $business,
            'user' => ['id' => $user->id, 'name' => $user->user_full_name],
            'currency' => $currency->toArray(),
        ]);
        
        if ($location_id) {
            $business_location = \App\BusinessLocation::find($location_id);
            session(['business_location' => $business_location]);
        }
    }
    
    /**
     * Formats an amount in the business's currency.
     *
     * @param float $amount
     * @param string $symbol
     * @return string
     */
    private function formatCurrency($amount, $symbol)
    {
        return number_format($amount, 2, '.', ',') . ' ' . $symbol;
    }

    /**
     * Endpoint for the "Find a Customer" tool.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|integer',
            'user_id' => 'required|integer',
            'customer_name' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $business_id = $request->input('business_id');
        $customerName = $request->input('customer_name');
        
        $this->setupBusinessSession($business_id, $request->input('user_id'));

        $customer = Contact::where('business_id', $business_id)
                            ->where('type', 'customer')
                            ->where('name', 'like', '%' . $customerName . '%')
                            ->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => "Désolé, je n'ai pas trouvé de client correspondant à '{$customerName}'."
            ], 404);
        }

        $currency_symbol = session('currency')['symbol'];
        $response_message = "Voici les informations pour le client " . $customer->name . ":\n"
                  . "- Nom: " . $customer->name . "\n"
                  . "- Téléphone: " . $customer->mobile . "\n"
                  . "- Email: " . $customer->email . "\n"
                  . "- Solde dû: " . $this->formatCurrency($customer->balance, $currency_symbol);

        return response()->json([
            'success' => true,
            'message' => $response_message,
            'data' => $customer
        ]);
    }

    /**
     * Endpoint for the "Close Cash Register" tool.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function closeCashRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $this->setupBusinessSession($request->input('business_id'), $request->input('user_id'));
        $cashRegisterUtil = new CashRegisterUtil();

        $register = CashRegister::where('user_id', $request->input('user_id'))
                                ->where('business_id', $request->input('business_id'))
                                ->where('status', 'open')
                                ->first();

        if (empty($register)) {
            return response()->json(['success' => false, 'message' => "Aucune caisse n'est actuellement ouverte pour cet utilisateur."], 404);
        }

        $register_details = $cashRegisterUtil->getRegisterDetails($register->id);
        $cashRegisterUtil->closeRegister($register->id, "Fermeture automatique via H360Copilot.");

        $currency_symbol = session('currency')['symbol'];
        $response_message = "Caisse fermée avec succès.\n\n"
            . "Résumé de la session :\n"
            . "- Total des paiements en espèces: " . $this->formatCurrency($register_details['total_cash'], $currency_symbol) . "\n"
            . "- Total des ventes: " . $this->formatCurrency($register_details['total_sale'], $currency_symbol);

        return response()->json([
            'success' => true,
            'message' => $response_message,
            'data' => $register_details
        ]);
    }

    /**
     * Endpoint for the "Start Inventory Count" tool.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startInventoryCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|integer',
            'user_id' => 'required|integer',
            'location_id' => 'required|integer',
            'ref_no' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }
        
        $this->setupBusinessSession($request->input('business_id'), $request->input('user_id'), $request->input('location_id'));
        
        DB::beginTransaction();
        try {
            $transactionUtil = new TransactionUtil();
            $input_data = [
                'transaction_date' => Carbon::now(),
                'location_id' => $request->input('location_id'),
                'type' => 'stock_adjustment',
                'status' => 'draft',
                'adjustment_type' => 'stock_count',
                'final_total' => 0,
                'total_amount_recovered' => 0,
                'additional_notes' => 'Inventaire initié via H360Copilot',
            ];
            
            if (!empty($request->input('ref_no'))) {
                $input_data['ref_no'] = $request->input('ref_no');
            } else {
                $ref_count = $transactionUtil->setAndGetReferenceCount('stock_adjustment');
                $input_data['ref_no'] = $transactionUtil->generateReferenceNumber('stock_adjustment', $ref_count);
            }

            $stock_adjustment = Transaction::create($input_data);
            DB::commit();

            $response_message = "Nouvelle session d'inventaire lancée avec succès.\n"
                              . "Le numéro de référence est : " . $stock_adjustment->ref_no . ".\n";

            return response()->json([
                'success' => true, 'message' => $response_message,
                'data' => ['transaction_id' => $stock_adjustment->id, 'ref_no' => $stock_adjustment->ref_no]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => "Erreur: " . $e->getMessage()], 500);
        }
    }

    /**
     * Endpoint for the "Generate Reorder Suggestion" tool.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateReorderSuggestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'required|integer',
            'user_id' => 'required|integer',
            'location_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $this->setupBusinessSession($request->input('business_id'), $request->input('user_id'), $request->input('location_id'));

        try {
            $products_to_reorder = Product::join('variations as v', 'products.id', '=', 'v.product_id')
                ->join('variation_location_details as vld', 'v.id', '=', 'vld.variation_id')
                ->where('products.business_id', $request->input('business_id'))
                ->where('vld.location_id', $request->input('location_id'))
                ->whereNotNull('products.alert_quantity')
                ->where('products.enable_stock', 1)
                ->whereRaw('vld.qty_available <= products.alert_quantity')
                ->select('products.name as product_name', 'v.name as variation_name', 'v.sub_sku', 'products.alert_quantity', 'vld.qty_available')
                ->get();

            if ($products_to_reorder->isEmpty()) {
                return response()->json(['success' => true, 'message' => "Bonne nouvelle ! Tous les niveaux de stock sont au-dessus du seuil d'alerte."]);
            }

            $response_message = "Voici les produits qui nécessitent un réapprovisionnement :\n\n";
            $products_data = [];

            foreach ($products_to_reorder as $product) {
                $product_full_name = $product->product_name . (($product->variation_name != 'DUMMY') ? ' - ' . $product->variation_name : '');
                $response_message .= "- " . $product_full_name . " (SKU: " . $product->sub_sku . ")\n"
                                  . "  Stock actuel: " . (int)$product->qty_available . ", Seuil d'alerte: " . (int)$product->alert_quantity . "\n";
                $products_data[] = ['name' => $product_full_name, 'sku' => $product->sub_sku, 'current_stock' => (int)$product->qty_available, 'alert_quantity' => (int)$product->alert_quantity];
            }

            return response()->json(['success' => true, 'message' => $response_message, 'data' => $products_data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => "Erreur: " . $e->getMessage()], 500);
        }
    }
}

