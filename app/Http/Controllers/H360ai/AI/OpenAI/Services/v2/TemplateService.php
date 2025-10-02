<?php

/**
 * @package ContentService
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 07-03-2023
 */

 namespace Modules\OpenAI\Services\v2;

use App\Models\{
    Language,
    User,
    Team
};

use App\Traits\ApiResponse;
use Modules\OpenAI\Entities\{
    Content,
    UseCase,
    Option,
    Archive
};
use Modules\OpenAI\Services\ContentService;
use Modules\OpenAI\Services\UseCaseTemplateService;
use App\Facades\AiProviderManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use App\Models\TeamMemberMeta;


 class TemplateService
 {
    use ApiResponse;

    protected $formData;
    /**
     * The AI provider used for generating template responses.
     *
     * This private property holds the AI provider used for generating template responses
     * within the `TemplateResponse` class. It encapsulates the provider information,
     * allowing it to be accessed and utilized internally within the class only.
     *
     * @var mixed $aiProvider The AI provider used for Template generation.
     */
    private $aiProvider;

    /**
     * @var string The type of service.
     */
    protected $type = 'template';

    
    /**
     * @var string The type of chat, default chat is template.
     */
    protected $chatType = 'template';

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct()
    {
        if (! is_null(request('provider'))) {
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'templatecontent');
            manageProviderValues(request('provider'), 'model', 'templatecontent');
        }
    }

    /**
     * Prepares data by verifying the existence of an AI provider and then storing the data.
     *
     *
     * @throws \Exception If the AI provider is not set.
     * @return mixed The result of the `store` method.
     */
    public function prepareData(): string
    {
        if (! $this->aiProvider) {
            throw new \Exception(__('Provider not found.'));
        }

        return $this->store();
    }

    /**
     * Prepare the template data.
     *
     * This method retrieves the use case by its slug from the request, initializes a new template service with the use case prompt,
     * sets the template variables, filters any bad words in the rendered template, and stores the processed data in the formData property.
     *
     * @return void
     */
    public function templateData(): void
    {
        $useCase = (new ContentService())->useCasebySlug(request('useCase'));
        $templateService = new UseCaseTemplateService($useCase->prompt);
        $templateService->setVariables(json_decode(request('questions'), true));
        $this->formData['prompt'] = filteringBadWords($templateService->render());
        $this->formData['use_case_id'] = $useCase->id;
    }

    /**
     * Set the form data with the validated data.
     *
     * @param mixed $validatedData The validated data to set as form data.
     * @return void
     */
    public function initiate($validatedData): void
    {
        $this->formData = $validatedData;
    }

    /**
     * Store a new chat record and user's chat reply.
     *
     * @return int The ID of the newly created chat record.
     */
    public function store(): string
    {
       
        $promtText = array_values(json_decode(request()->questions, true));
        $promt = filteringBadWords(implode(',', $promtText));
        
        // Creates a new chat record.
        $parent = ArchiveService::create([
            'title' => $promt,
            'unique_identifier' => \Str::uuid(),
            'provider' => $this->aiProvider->alias(),
            'type' => $this->type . "_chat"
        ]);

        // Store user's chat reply.
        ArchiveService::create([
            'parent_id' => $parent->id,
            'user_id' => auth()->id(),
            'type' => $this->chatType . "_chat_reply",
            'user_reply' => $promt
        ]);
        return $parent->id;
    }

    /**
     * Process the data for the template generation.
     *
     * This method handles the processing of data for template generation, including
     * checking user subscriptions, generating AI content, and streaming the response.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse The streamed response.
     * @throws \Exception If there is an issue with user status or subscription validation.
     */

    public function processData(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $parentId = request('template_id');
        $promptText = array_values(json_decode(request('questions'), true));
        $prompt = filteringBadWords(implode(',', $promptText));
        $subscription = null;
        $userId = null;
        if (!subscription('isAdminSubscribed')) {
            $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
            $userStatus = (new ContentService())->checkUserStatus($userId, 'meta');
            if ($userStatus['status'] == 'fail') {
                throw new \Exception($userStatus['message']);
            }
            $validation = subscription('isValidSubscription', $userId, 'word');
            $subscription = subscription('getUserSubscription', $userId);
            if ($validation['status'] == 'fail' && !auth()->user()->hasCredit('word')) {
                throw new \Exception($validation['message']);
            }
        }

        return response()->stream(function () use($prompt, $parentId, $userId, $subscription) {

            $text = "";

            try {

                $result = $this->aiProvider->templateGenerate($this->formData);
                $totalTokens = 0;  // dynamically obtained
                if (!empty($result)) {
                    foreach ($result as $response) {
                        $raw = $this->aiProvider->templateStreamData($response);
    
                        if (isset($raw)) {
                            $clean = str_replace(["\r\n", "\r", "\n"], "<br/>", $raw);
                            $text .= $raw;
                            $totalTokens++; // dynamically obtained
                            echo 'data: ' . $clean ."\n\n";
                            ob_flush();
                            flush();
                            usleep(400);
                        }
    
                        if (connection_aborted()) { 
                            break; 
                        }
    
                        if (is_null($text)) {
                            break;
                        }
                    }
                }

            } catch (\Exception $exception) {
                echo "event: error\n";
                echo "data: " . $exception->getMessage();
                echo "\n\n";
                ob_flush();
                flush();
                usleep(50000);
            }

            if ($result) {
                $totalWords = preference('word_count_method') == 'token' ? subscription('tokenToWord', $totalTokens) : countWords($text);

                // Creates a bot reply record for the specified parent chat
                ArchiveService::create([
                    'parent_id' => $parentId,
                    'template_creator_id' => auth()->id(),
                    'provider' => $this->aiProvider->alias(),
                    'expense' => $totalTokens,
                    'use_case_id' => $this->formData['use_case_id'],
                    'template_level' => request('creativity_level'),
                    'template_language' => request('language'),
                    'template_variant' => request('variant'),
                    'template_model' => request('model'),
                    'template_tone' => request('tone'),
                    'expense_type' => preference('word_count_method'),
                    'type' => $this->type,
                    'template_title' => $prompt,
                    'slug' => $this->createSlug($prompt),
                    'content' => $text,
                    'balanceReduce' => ! subscription('isAdminSubscribed') || auth()->user()->hasCredit('word') ? 'subscription' : 'onetime',
                    'total_words' => $totalWords,
                ]);

                if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
                    $increment = subscription('usageIncrement', $subscription?->id, 'word', $totalWords, $userId);
                    if ($increment  && $userId != auth()->user()->id) {
                        $this->storeTeamMeta($totalWords);
                    }
                }
            }

            echo 'data: [DONE]';
            echo "\n\n";
            ob_flush();
            flush();
            usleep(40000);
            
            
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'text/event-stream',
        ]);
    }

    /**
     * Create slug
     *
     * @param mixed $name
     * @return string
     */
    protected function createSlug($name): ?string
    {
        if (!empty($name)) {

            $slug = cleanedUrl($name);

            $template =  $this->model()->with(['user', 'childs', 'metas'])
            ->when(function ($query) use ($slug) {
                return $query->whereHas('metas', function ($q) use ($slug) {
                    $q->where('key', 'slug')->where('value', $slug);
                });
            })
            ->where('type', 'template')->first();
        
            if ($template) {
                $slug = $slug . '-' . time();
            }

            return $slug;
        }
    }

     /**
     * Team member meta insert or update
     * @param array $imagedata
     *
     * @return bool|array
     */
    public function storeTeamMeta($words)
    {
        $memberData = Team::getMember(auth()->user()->id);
        if (!empty($memberData)) {
            $usage = TeamMemberMeta::getMemberMeta($memberData->id, 'word_used');
            if (!empty($usage)) {
                return $usage && $usage->increment('value', $words); 
            }
        }
        return false;
    }

    /**
     * Get All Data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        $result = Archive::with(['metas', 'user', 'useCase:id,name', 'templateCreator']);

        return $result->whereType('template')->whereHas('metas', function ($query) {
            $query->where('key', 'template_creator_id')->where('value', auth()->user()->id);
        })->latest();
    }

     /**
     * Get All Favorite
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFavourite()
    {
        $bookmarks = auth()->user()->document_bookmarks_openai;

        $result = $this->model()->with(['useCase'])->whereIn('id', $bookmarks)
        ->whereHas('metas', function ($query) {
            $query->where('key', 'template_creator_id')->where('value', auth()->user()->id);
        });
        
        return $result->where('type', 'template')->latest();
    }

    /**
     * Content By Slug
     *
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function contentBySlug($slug): Archive
    {
        return $this->model()
            ->whereHas('metas', function ($query) use ($slug) {
                $query->where('key', 'slug')->where('value', $slug);
            })
            ->firstOrFail();
    }

    /**
     * Use Cases
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function useCases($data = null): Collection
    {
        $useCases = UseCase::where('status', 'Active')->get();

        if ($data != null) {

            $favUseCases = $useCases->whereIn('id', $data);
            $exceptFavUseCases = $useCases->whereNotIn('id', $data);

            $useCases =  $favUseCases->merge($exceptFavUseCases);

        }

        return $useCases;

    }

    /**
     * Language Data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function languages(): Collection
    {
        return Language::where(['status' => 'Active'])->get();
    }

    /**
     * Users Data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function users(): Collection
    {
        return User::where(['status' => 'Active'])->get();
    }

    /**
     * Core Model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function model(): Builder
    {
        return Archive::with(['metas', 'user:id,name']);
    }

    /**
     * Get Content
     *
     * @param mixed $contentId
     * @return array
     */
    public function getContent($contentId): array
    {
        $data['partialContent'] = Content::with(['useCase', 'option'])->where('id', $contentId)->firstOrFail();
        $data['questions'] = $this->getQuestions($data['partialContent']->title);
        $data['wrodCount'] = countWords($data['partialContent']->content);
        return $data;
    }

    /**
     * Get Question
     *
     * @param string $string
     * @return array
     */
    protected function getQuestions($string): array
    {
        return explode("," , $string);
    }
    /**
     * get use case by slug
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function useCasebySlug($name)
    {
        return UseCase::where('status', 'active')->whereSlug($name)->firstOrFail();
    }

    /**
     * get use case by slug
     *
     * @param int $useCaseId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOption($useCaseId): Collection
    {
        return Option::whereUseCaseId($useCaseId)->get();
    }

    /**
     * @param mixed $id
     *
     * @return [type]
     */
    public function delete(int $id): JsonResponse
    {
        try {
            ArchiveService::delete($id, 'template');
            $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Content')])];

        } catch (\Exception $e) {
            $response = ['status' => 'fail', 'message' => $e->getMessage()];
        }
        return response()->json($response);
    }

    /**
     * Update Content
     *
     * @param string $contentSlug
     * @param string $contents
     * @return \Illuminate\Http\Response
     */
    public function updateContent($contentSlug, $contents): JsonResponse
    {
        $response = ['status' => 'error', 'message' => __('The :x does not exist.', ['x' => __('Content')])];
        $content = $this->model()->with(['user', 'childs', 'metas'])
            ->when(function ($query) use ($contentSlug) {
                return $query->whereHas('metas', function ($q) use ($contentSlug) {
                    $q->where('key', 'slug')->where('value', $contentSlug);
                });
            })
            ->where('type', 'template')->first();

        if ($content) {
            $content->content = $contents;
            $content->save();
            $response = ['status' => 'success', 'message' => __('The Content Updated successfully.')];
        }

        return response()->json($response);
    }

}
