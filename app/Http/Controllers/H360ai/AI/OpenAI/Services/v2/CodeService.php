<?php

/**
 * @package CodeService
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 01-04-2024
 */

 namespace Modules\OpenAI\Services\v2;


use Illuminate\Support\Str;

use App\Models\{
    Team,
    TeamMemberMeta
};
use App\Facades\AiProviderManager;
use Modules\OpenAI\Entities\{
    Archive
};
use Modules\OpenAI\Services\ContentService;
use Exception;
use Ramsey\Uuid\Type\Integer;

 class CodeService
 {
    /**
     * Form Data
     *
     * @var array|object
     */
    protected $formData = [];

    /**
     * The original API response received during initialization.
     *
     * This protected property stores the original API response received during the initialization
     * of the `CodeResponse` object. It encapsulates the API response data, allowing it to be accessed
     * within the class and its subclasses, but not directly from outside the class.
     *
     * @var mixed $response The original API response object.
     */
    protected $response;

    /**
     * @var string The type of chat, default chat is code.
     */
    protected $chatType = 'code';

    /**
     * @var string The type of service.
     */
    protected $type = 'code';

    /**
     * The AI provider used for generating code responses.
     *
     * This private property holds the AI provider used for generating code responses
     * within the `CodeResponse` class. It encapsulates the provider information,
     * allowing it to be accessed and utilized internally within the class only.
     *
     * @var mixed $aiProvider The AI provider used for code generation.
     */
    private $aiProvider;

    /**
     * @var array An array to store validated data.
     */
    protected $validatedData = [];

    /**
     * Prompt
     *
     * @var string
     */
    protected $prompt;

    /**
     * Initialize
     *
     * @param object $formData
     * @return void
     */
    public function __construct($formData = null, $prompt = null)
    {
        $this->formData = $formData;
        $this->prompt = $prompt;

        if (! is_null(request('provider'))) {
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'code');
        }
    }

    /**
     * Validates and sets the form data.
     *
     * This method validates the provided data against predefined rules and sets it
     * as the form data for further processing. It is typically used to validate and
     * prepare incoming data before storing or utilizing it within the class.
     *
     * @param mixed $validatedData The data to be validated and set as form data.
     * @return void
     */
    public function validate($validatedData): void
    {
        $this->formData = $validatedData;

        if (! is_null(request('provider'))) {
            manageProviderValues(request('provider'), 'model', 'code', ['code_level' => 'codeLevel']);
        }
    }

    /**
     * Image create
     *
     * @param mixed $data
     * 
     * @return string
     */
    public function prepareData(): string
    {
        $this->formData['prompt'] = filteringBadWords($this->formData['prompt']);

        return $this->preparePrompt();
    }

    /**
     * prepare prompt
     *
     * @return string
     */
    public function preparePrompt(): string|Integer
    {
        if (! $this->aiProvider) {
            throw new Exception(__('Provider not found.'));
        }

        $this->response =  $this->aiProvider->code($this->formData);

        return $this->save();
    }

    /**
     * Store Images
     * @param mixed $data
     *
     * @return mixed
     */
    public function save(): mixed
    {
        $code = $this->storeInArchive();
        $this->createUserReply($code->id);
        return $this->storeInArchiveMeta($code->id);
    }

    /**
     * Creates a new chat record.
     *
     * @return Archive The newly created chat instance.
     */
    protected function storeInArchive()
    {
        $code = new Archive();
        $code->title = $this->createName(request('prompt'));
        $code->unique_identifier = \Str::uuid();
        $code->provider = $this->aiProvider->alias();
        $code->type = $this->type;
        $code->save();

        return $code;
    }

    /**
     * Creates a user reply record for the specified parent chat.
     *
     * @param  int  $parentId  The ID of the parent chat.
     * 
     * @return Archive The newly created user reply instance.
     */
    protected function createUserReply($parentId)
    {
        $userReply = new Archive();
        $userReply->parent_id = $parentId;
        $userReply->user_id = auth()->id();
        $userReply->type = $this->chatType."_chat_reply";
        $userReply->user_reply = request('prompt');
        $userReply->save();

        return $userReply;
    }

     /**
     * Creates a bot reply record for the specified parent chat.
     *
     * @param  int  $parentId  The ID of the parent chat.
     * 
     * @return Archive The newly created user reply instance.
     */
    protected function storeInArchiveMeta($parentId)
    {
        $botReply = new Archive();
        $botReply->parent_id = $parentId;
        $botReply->code_creator_id = auth()->id();
        $botReply->raw_response = json_encode($this->response->response);
        $botReply->provider = $this->aiProvider->alias();
        $botReply->expense = $this->response->expense;
        $botReply->code_level = request('codeLevel');
        $botReply->code_language = request('language');
        $botReply->expense_type = 'token'; // dynamically obtained
        $botReply->type = $this->chatType."_chat_reply";
        $botReply->code_title = request('prompt');
        $botReply->code_model = request('model');
        $botReply->formated_code = json_encode($this->response->content);
        $botReply->slug = $this->createSlug(request('prompt'));
        $botReply->code = $this->response->content;
        $botReply->balanceReduce = 'onetime';

        if (! subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
            $botReply->balanceReduce = 'subscription';
        }
        $botReply->total_words = $this->response->word;
        
        $botReply->save();
        // Update Subscription and Credit.
        handleSubscriptionAndCredit(subscription('getUserSubscription', auth()->id()), 'word', $botReply->total_words, auth()->id());

        return $parentId;
    }

    /**
     * Code Name Creation
     *
     * @param null $name
     * @return string
     */
    public function createName($name = null)
    {
        return !empty($name) ? substr($name, 0, 100) : Str::random(100);
    }


    /**
     * Slug Creator
     *
     * @param string $name
     * @return string
     */
    protected function createSlug($name)
    {
        if (!empty($name)) {

            $slug = cleanedUrl($name);

            $code =  $this->model()->with(['user', 'childs', 'metas'])
            ->when(function ($query) use ($slug) {
                return $query->whereHas('metas', function ($q) use ($slug) {
                    $q->where('key', 'slug')->where('value', $slug);
                });
            })
            ->where('type', 'code_chat_reply')->first();
        
            if ($code) {
                $slug = $slug . '-' . time();
            }

            return $slug;
        }
    }

    /**
     * Core Model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function model(): \Illuminate\Database\Eloquent\Builder
    {
        return Archive::with(['metas', 'user:id,name']);
    }

    /**
     * Delete an item from the archive based on the provided request data.
     *
     * @param array $requestData The request data containing the ID of the item to delete.
     * @throws Exception If an error occurs during deletion.
     */
    public function delete(int $id, string $type)
    {
        try {
            ArchiveService::delete(
                (int) $id, 
                $type, 
                null, 
                ['key' => 'code_creator_id', 'value' => auth()->id()]
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

 }
