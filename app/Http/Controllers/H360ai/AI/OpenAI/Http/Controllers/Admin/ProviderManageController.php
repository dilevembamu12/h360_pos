<?php

namespace Modules\OpenAI\Http\Controllers\Admin;

use App\Facades\AiProviderManager;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Preference;
use Session;

class ProviderManageController extends Controller
{
    public function providers(Request $request, $segment = null)
    {
        $data['features'] = AiProviderManager::features();
        $data['featureName'] = $segment === null ? $data['features'][0]['key'] : $segment;
        $data['providers'] = AiProviderManager::featureSupportedProviders($data['featureName']);

        return view('openai::admin.manage-providers.index', $data);
    }

    public function manageProvider(Request $request)
    {
        
        $urlSegements = $request->segments();

        $providerName = str_replace(['_', '-'], '' , $urlSegements[count($urlSegements) - 1 ]);
        $featureName = str_replace(['_', '-'], '' , $urlSegements[count($urlSegements) - 2 ]);

        $data = ['status' => 'error', 'message' => __('The :x options for :y could not be updated. Please try again.', ['x' => $featureName, 'y' => $providerName])];
        
        $data['featureOptions'] = ($providerInstance = AiProviderManager::find($providerName))
            &&
            method_exists(
                $providerInstance,
                $featureMethod = $featureName . 'Options'
            )
            ? $providerInstance->$featureMethod()
            : [];

        // Update feature options with the request values
        if ($request->isMethod('POST')) {
            if (config('openAI.is_demo')) {
                return redirect()->back()->withErrors(__("Demo Mode! This action can't be perform."));
            }

            $this->validation($featureName, $providerName, $request);
            $temp = [];
            
            foreach ($data['featureOptions'] as $fields) {
                if (isset($request[$fields['name']])) {

                    if ($fields['name'] === 'language') {
                        $fields['value'] = $request[$fields['name']];
                    }

                    if ( $fields['type'] == "dropdown-with-image") {
                        $fields['value'] = array_map('json_decode', $request[$fields['name']]);
                    } else {
                        $fields['value'] = is_array($fields['value']) ? array_intersect($fields['value'], $request[$fields['name']]) : $request[$fields['name']];
                    }
                  
                } else {
                    $fields['value'] = is_array($fields['value']) ? [] : '';
                }
                $temp[] = $fields;
            }
            $preference = [
                'category' => $featureName,
                'field' => $featureName .'_'. $providerName,
                'value' => json_encode($temp)
            ];

            if (Preference::storeOrUpdate($preference)) {
                $data = ['status' => 'success', 'message' => __('The :x options for :y has been successfully updated.', ['x' => $featureName, 'y' => $providerName])];
            }

            Session::flash($data['status'], $data['message']);
            return back();
        }

        // Feature menu list
        $data['features'] = AiProviderManager::features(); 
        $data['featureName'] = $featureName;
        $data['providerName'] = $providerName;
        
        // Saved options
        $data['fields'] = json_decode(preference($featureName . '_' . $providerName), true) ?? [];

        $values1 = $values2 = [];
        foreach ($data['fields'] as $item1) {
            if ($item1['name'] === 'language') {
                $values1 = $item1['value'];
                break;
            }
        }
    
        foreach ($data['featureOptions'] as &$item2) {
            if ($item2['name'] === 'language') {
                $values2 = $item2['value'];
                $missingValues = array_diff($values1, $values2);
                $item2['value'] = array_merge($item2['value'], $missingValues);
                break;
            }
        }
        return view('openai::admin.manage-providers.feature_options', $data);
    }

    /**
     * Validate the request based on the feature and provider-specific rules.
     * 
     * @param string $featureName The name of the feature to validate.
     * @param string $providerName The name of the AI provider.
     * @param \Illuminate\Http\Request $request The HTTP request object to validate.
     * 
     * @throws \Illuminate\Validation\ValidationException If validation fails.
     */
    private function validation(string $featureName, string $providerName, Request $request)
    {
        $feature = AiProviderManager::features($featureName)[0] ?? null;

        if ($feature) {
            // Construct the data processor class name based on the feature
            $dataProcessor = preg_replace('/\s+/', '', $feature['name']) . 'DataProcessor';

            // Fetch the AI provider instance
            $aiProvider = AiProviderManager::find($providerName);

            if ($aiProvider) {
                // Get validation rules from the provider's data processor
                $validationRules = $aiProvider->getValidationRules($dataProcessor);

                // Validate the request using the obtained rules
                $request->validate($validationRules);
            }
        }
    }
}
