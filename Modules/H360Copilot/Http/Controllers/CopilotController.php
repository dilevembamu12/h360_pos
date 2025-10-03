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
        /*
        $user_context=[
            "user_id" => auth()->user()->id,
            "user_type" => auth()->user()->user_type,
            "surname" => auth()->user()->surname,
            "first_name" => auth()->user()->first_name,
            "last_name" => auth()->user()->last_name,
            "username" => auth()->user()->username,
            "email" => auth()->user()->username,
            "language" => "Repond toujours cet utilisateur en langue " .auth()->user()->language,
            "business_id" => auth()->user()->business_id,
            "essentials_department_id" => auth()->user()->essentials_department_id,
            "essentials_designation_id" => auth()->user()->essentials_designation_id,
            "essentials_salary" => auth()->user()->essentials_salary,
            "essentials_pay_period" => auth()->user()->essentials_pay_period,
            "essentials_pay_cycle" => auth()->user()->essentials_pay_cycle,
            "available_at" => auth()->user()->available_at,
            "paused_at" => auth()->user()->paused_at,
            "max_sales_discount_percent" => auth()->user()->max_sales_discount_percent,
            "crm_contact_id" => auth()->user()->crm_contact_id,
            "is_cmmsn_agnt" => auth()->user()->is_cmmsn_agnt,
            "cmmsn_percent" => auth()->user()->cmmsn_percent,
            "selected_contacts" => auth()->user()->selected_contacts,
            "gender" => auth()->user()->gender,
            "marital_status" => auth()->user()->marital_status,
            "blood_group" => auth()->user()->blood_group,
            "contact_number" => auth()->user()->contact_number,
            "alt_number" => auth()->user()->alt_number,
            "family_number" => auth()->user()->family_number,
            "fb_link" => auth()->user()->fb_link,
            "twitter_link" => auth()->user()->twitter_link,
            "social_media_1" => auth()->user()->social_media_1,
            "social_media_2" => auth()->user()->social_media_2,
            "permanent_address" => auth()->user()->permanent_address,
            "current_address" => auth()->user()->current_address,
            "guardian_name" => auth()->user()->guardian_name,
            "bank_details" => auth()->user()->bank_details,
            "id_proof_name" => auth()->user()->id_proof_name,
            "id_proof_number" => auth()->user()->id_proof_number,
            #"location_id" => auth()->user()->location_id,
            "crm_department" => auth()->user()->crm_department,
            "crm_designation" => auth()->user()->crm_designation,
            "created_at" => auth()->user()->created_at,
            "updated_at" => auth()->user()->updated_at,
        ];
        dd(auth()->user()->locations());
        */
        $request->merge(['business_id' => auth()->user()->business_id]);
        $request->merge(['user_id' => auth()->user()->id]);
        $request->validate([
            'prompt' => 'required|string|max:2000',
            'business_id' => 'required',
            'user_id' => 'required',
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