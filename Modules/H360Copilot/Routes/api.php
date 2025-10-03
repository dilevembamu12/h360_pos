<?php

use Illuminate\Http\Request;

use Modules\H360Copilot\Http\Controllers\ApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/h360copilot', function (Request $request) {
    return $request->user();
});


Route::prefix('v1/copilot')
    ->middleware([\Modules\H360Copilot\Http\Middleware\VerifyCopilotToken::class])
    ->group(function () {
        // Endpoint pour l'outil "Trouver un client"
        Route::post('/find-customer', [ApiController::class, 'findCustomer']);

        // --- Outils de Procédure ---
        Route::post('/close-cash-register', [ApiController::class, 'closeCashRegister']);
        Route::post('/start-inventory-count', [ApiController::class, 'startInventoryCount']);
        Route::post('/generate-reorder-suggestion', [ApiController::class, 'generateReorderSuggestion']);
        

        
        // --- Ajoutez vos futurs endpoints ici ---
        // Route::post('/create-invoice', [ApiController::class, 'createInvoice']);
        // Route::post('/get-sales-report', [ApiController::class, 'getSalesReport']);
});
