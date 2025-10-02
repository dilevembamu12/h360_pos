<?php

namespace Modules\H360Copilot\Lib\Tools;

use App\Contact;

class FindCustomerTool
{
    // Décrit l'outil pour l'IA
    public static function definition(): array
    {
        return [
            'name' => 'find_customer',
            'description' => 'Recherche un client par son nom et retourne ses informations de base.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string',
                        'description' => 'Le nom complet ou partiel du client à rechercher.',
                    ],
                ],
                'required' => ['name'],
            ],
        ];
    }

    // Exécute l'action de l'outil
    public static function execute($params, $business_id)
    {
        $customer = Contact::where('business_id', $business_id)
            ->where('type', 'customer')
            ->where('name', 'like', "%{$params['name']}%")
            ->select('name', 'mobile', 'email', 'supplier_business_name')
            ->first();

        if ($customer) {
            // Renvoie le résultat au format texte (JSON) que l'IA peut lire
            return json_encode($customer->toArray());
        }

        return "Aucun client trouvé avec le nom '{$params['name']}'.";
    }
}