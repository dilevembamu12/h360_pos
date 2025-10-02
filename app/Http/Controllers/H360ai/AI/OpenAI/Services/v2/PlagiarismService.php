<?php 

namespace Modules\OpenAI\Services\v2;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Facades\AiProviderManager;
use Illuminate\Http\Response;
use Modules\OpenAI\Services\ContentService;
use Exception;

class PlagiarismService
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
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'plagiarism');
        }
    }

    /**
     * Handle the plagiarism check request by preparing the input data and sending it
     * to the AI provider for plagiarism detection.
     * 
     * @param array $requestData
     * 
     * @return mixed
     *
     * @throws \Exception If the plagiarism check fails or returns an error message.
     */
    public function handlePlagiarism($requestData): mixed
    {
        if (! $this->aiProvider) {
            throw new Exception(__(':x provider is not available for the :y. Please contact the administration for further assistance.', ['x' => request('provider'), 'y' => __('Plagiarism')]));
        }

        $response = [];
        try {
            $plagiarism = $this->aiProvider->plagiarism($requestData);
            
            $response = [
                'balanceReduce' => 'onetime',
                'pages' => $plagiarism->expense,
            ];

            $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
            $subscription = subscription('getUserSubscription', $userId);

            if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('page')) {
                $increment = subscription('usageIncrement', $subscription?->id, 'page', $plagiarism->expense, $userId);
                $response['balanceReduce'] = app('user_balance_reduce');

                if ($increment  &&  (new ContentService())->getCurrentMemberUserId('meta', null) != auth()->user()->id) {
                    (new TeamMemberService)->updateTeamMeta('page', $plagiarism->expense);
                }
            }

            if (empty($plagiarism->content)) {
                throw new Exception(__('Something went wrong. Please try again.'));
            }

            $result = $this->aiProvider->getPlagiarismReport($plagiarism->content);

            if (empty($result->report)) {
                throw new Exception(__('Something went wrong. Please try again.'));
            }

            return array_merge($response, $result->report);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }
}
