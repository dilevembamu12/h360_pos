<?php 

namespace Modules\OpenAI\Services\v2;

use App\Facades\AiProviderManager;
use Illuminate\Http\Response;
use Exception;

class AiDetectorService
{
    /**
     * @var \App\Facades\AiProviderManager  The AI provider manager instance.
     */
    private $aiProvider;

    /**
     * Method __construct
     *
     * @param $generator [decide which AI provider will be used for generate]
     *
     * @return void
     */
    public function __construct() 
    {
        if(! is_null(request('provider'))) {
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'aidetector');
        }
    }

    /**
     * Handle the aidetector check request by preparing the input data and sending it
     * to the AI provider for aidetector detection.
     * 
     * @param array $requestData
     * 
     * @return mixed
     *
     * @throws \Exception If the aidetector check fails or returns an error message.
     */
    public function handleAiDetector($requestData): mixed
    {
        if (! $this->aiProvider) {
            throw new Exception(__('Provider not found.'));
        }

        $this->featurePreference();

        $response = [];
        try {
            $validationData = $this->aiProvider->getCustomerValidationRules('AiDetectorDataProcessor');

            $rules = $validationData[0] ?? []; // Default to an empty array if not set
            $messages = $validationData[1] ?? []; // Default to an empty array if not set

            request()->validate($rules, $messages);

            $aiDetector = $this->aiProvider->aiDetector($requestData);
            $userId = auth()->id();
            $response = [
                'balanceReduce' => 'onetime',
                'pages' => $aiDetector->expense,
            ];

            $subscription = subscription('getUserSubscription', $userId);

            if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('page')) {
                $increment = subscription('usageIncrement', $subscription?->id, 'page', $aiDetector->expense, $userId);
                $response['balanceReduce'] = app('user_balance_reduce');

                if ($increment  &&  (new ContentService())->getCurrentMemberUserId('meta', null) != auth()->user()->id) {
                    (new TeamMemberService)->updateTeamMeta('page', $aiDetector->expense);
                }
            }

            if (empty($aiDetector->content)) {
                throw new Exception(__("We\'re unable to analyze your content at the moment. Please give it another try."));
            }

            $result = $this->aiProvider->getAiDetectorReport($aiDetector->content);

            if (empty($result->report)) {
                throw new Exception(__('We\'re unable to analyze your content at the moment. Please give it another try.'));
            }

            return array_merge($response, $result->report);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    /**
     * Validates feature preferences based on user request data.
     *
     * @throws \Exception If a requested feature is disabled.
     *
     * @return void
     */
    private function featurePreference() {
        $service = new FeaturePreferenceService();
        $data = $service->processData('ai_detector')['feature_options'];
        $newArray = ['file' => 'file_upload', 'text' => 'content_description'];

        $requestData = request()->only('file', 'text');
        foreach ($requestData as $type => $value) {
            if (isset($newArray[$type]) && !$data[$newArray[$type]]) {
                throw new Exception(__(':x feature has been disabled. For assistance, please contact the administration.', ['x' => ucfirst(str_replace('_', ' ', $newArray[$type]))]));
            }
        }
    }
}
