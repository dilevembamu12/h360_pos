<?php

use Modules\H360Copilot\Http\Controllers\CopilotController;
use Modules\H360Copilot\Http\Controllers\MCPController;

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::post('/h360-copilot/ask', [CopilotController::class, 'ask'])->name('h360_copilot.ask');

    Route::post('/h360-copilot/execute-tool', [CopilotController::class, 'executeToolFromWebhook'])->name('h360_copilot.execute_tool');

    // Route pour que n8n parle à notre serveur d'outils (MCP)
    Route::any('/mcp/h360copilot', [MCPController::class, 'handle'])->name('h360_copilot.mcp');
});