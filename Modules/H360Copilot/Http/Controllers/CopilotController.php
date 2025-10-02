<?php

namespace Modules\H360Copilot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CopilotController extends Controller
{
    /**
     * Envoie le prompt de l'utilisateur à l'agent n8n.
     */
    public function ask(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:2000',
        ]);

        $n8nWebhookUrl = env('N8N_AGENT_WEBHOOK_URL'); // Utilisez une variable dédiée

        if (empty($n8nWebhookUrl)) {
            Log::error('N8N_AGENT_WEBHOOK_URL non configuré.');
            return response()->json(['error' => 'Assistant IA non configuré.'], 500);
        }

        try {
            // Transmet simplement la requête
            $response = Http::timeout(60)->post($n8nWebhookUrl, $request->all());

            return $response->json();

        } catch (\Exception $e) {
            Log::emergency("Erreur de communication avec l'agent n8n: " . $e->getMessage());
            return response()->json(['error' => 'Impossible de contacter l\'agent IA.'], 500);
        }
    }
}