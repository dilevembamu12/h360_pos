<?php

namespace Modules\H360Copilot\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\H360Copilot\Lib\Tools\FindCustomerTool;

class MCPController extends Controller
{
    /**
     * Registre de tous les outils disponibles pour l'IA.
     */
    protected $tools = [
        'find_customer' => FindCustomerTool::class,
        // Vous ajouterez vos futurs outils ici
    ];

    /**
     * Point d'entrée principal pour le serveur MCP.
     */
    public function handle(Request $request)
    {
        // La requête de n8n pour lister les outils est un GET
        if ($request->isMethod('get')) {
            return $this->listTools();
        }

        // La requête de n8n pour exécuter un outil est un POST
        if ($request->isMethod('post')) {
            return $this->executeTool($request);
        }

        return response()->json(['error' => 'Méthode non autorisée'], 405);
    }

    /**
     * Renvoie la liste des définitions des outils.
     */
    private function listTools()
    {
        $tool_definitions = [];
        foreach ($this->tools as $name => $class) {
            if (method_exists($class, 'definition')) {
                $tool_definitions[] = $class::definition();
            }
        }
        return response()->json($tool_definitions);
    }

    /**
     * Exécute un outil spécifique demandé par n8n.
     */
    private function executeTool(Request $request)
    {
        $toolName = $request->input('name');
        $params = $request->input('params', []);
        
        Log::info("MCP Server: Exécution de l'outil '{$toolName}'", $params);

        if (isset($this->tools[$toolName])) {
            try {
                $toolClass = $this->tools[$toolName];
                $business_id = auth()->user()->business_id;

                $result = $toolClass::execute($params, $business_id);

                return response()->json(['result' => $result]);

            } catch (\Exception $e) {
                Log::error("Erreur d'exécution de l'outil '{$toolName}': " . $e->getMessage());
                return response()->json(['error' => "Erreur lors de l'exécution de l'outil."], 500);
            }
        }

        Log::warning("MCP Server: Outil inconnu demandé : '{$toolName}'");
        return response()->json(['error' => 'Outil inconnu'], 404);
    }
}