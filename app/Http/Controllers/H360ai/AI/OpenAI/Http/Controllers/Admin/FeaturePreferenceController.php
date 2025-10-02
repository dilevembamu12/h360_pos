<?php

namespace Modules\OpenAI\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\OpenAI\Entities\FeaturePreference;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Language;
use Modules\OpenAI\Services\v2\FeatureManagerService;


class FeaturePreferenceController extends Controller
{
    /**
     * Display the feature preferences.
     * 
     * @return \Illuminate\View\View The feature preference's view.
     */
    public function manageFeature()
    {
        
        $service = new FeatureManagerService();

        $preferences = FeaturePreference::with('metas')->get();
        $data = [
            'allLanguages' => Language::where('status', 'Active')->get(),
            'sidebarLists' => [],
            'features' => []
        ];

        foreach ($preferences as $preference) {
            foreach ($preference->metas as $meta) {
                $metaValue = json_decode($meta->value, true);
                $data['sidebarLists'][$preference->slug][] = $meta->key;
                $data['features'][$preference->slug][$meta->key] = $metaValue;
            }

            if ($preference->slug === 'ai_doc_chat') {
                $providers = $service->getActiveProviders('aiembedding') ?? [];
                $data['features']['ai_doc_chat']['general_options']['providers'] = $providers;
            
                $providerModels = [];
                foreach ($providers as $provider) {
                    $providerModels[$provider] = $service->getModels('aiembedding', $provider);
                }
            
                $data['features']['ai_doc_chat']['general_options']['providerModels'] = $providerModels;
            }
        }

        return view('openai::admin.feature-preference.feature_options', $data);
    }

    /**
     * Store the feature preference options.
     *
     * @param Request $request The request object containing the new options and file ID.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a status message.
     * @throws \Exception If an error occurs during the database transaction.
     */
    public function store(Request $request)
    {
        $data = ['status' => 'fail', 'message' => __('Something went wrong')];
        
        $slugs = array_keys($request->except('_token'));
        $preferences = FeaturePreference::with('metas')->whereIn('slug', $slugs)->get();
        
        if ($preferences->isEmpty()) {
            Session::flash($data['status'], $data['message']);
            return back();
        }
        
        try {
            DB::beginTransaction();
        
            foreach ($preferences as $preference) {
                // Update preference options
                $options = $request->get($preference->slug, []);
                foreach ($options as $optionKey => $option) {
                    $preference->$optionKey = json_encode($option);
                }
                $preference->save();
            }

            $data['status'] = 'success';
            $data['message'] = __('The :x has been successfully saved.', ['x' => __('Feature Preference')]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        Session::flash($data['status'], $data['message']);
        return back();
    }
}
