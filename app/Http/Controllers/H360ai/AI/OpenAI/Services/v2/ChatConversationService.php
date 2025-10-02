<?php

namespace Modules\OpenAI\Services\v2;

use Modules\OpenAI\Entities\{
    EmbededResource,
    Archive
};
use Illuminate\Support\Facades\DB;
use Modules\OpenAI\Entities\ChatBot;
use Modules\OpenAI\Services\ContentService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Facades\AiProviderManager;
use Exception;


class ChatConversationService
{
    /**
     * @var string The type of chat, default chat is doc_chat.
     */
    protected $chatType = 'chatbot_chat';

    /**
     * @var mixed The ID of the user.
     */
    protected $userId;

    /**
     * @var int The chunk data, default value is 4.
     */
    protected $chunkData = 4;

    /**
     * The AI provider used for generating chat responses.
     *
     * This private property holds the AI provider used for generating chat responses
     * within the `ChatResponse` class. It encapsulates the provider information,
     * allowing it to be accessed and utilized internally within the class only.
     *
     * @var mixed $aiProvider The AI provider used for chat generation.
     */
    private $aiProvider;

    /**
     * The model of the AI provider used for chat responses.
     *
     * @var string $aiProviderModel The specific model of the AI provider.
     */
    private $aiProviderModel;

    /**
     * The AI provider used for generating embeddings..
     *
     * @var mixed $aiEmbeddingProvider The AI provider instance or configuration used for embedding generation.
     */
    private $aiEmbeddingProvider;

    /**
     * The model of the AI embedding provider.
     *
     * @var string $aiEmbeddingProviderModel The specific model of the embedding provider.
     */
    private $aiEmbeddingProviderModel;

    /**
     * @var array An array to store validated data.
     */
    protected $validatedData = [];

    /**
     * Constructor method.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userId = $this->getBotOwnerId(request('chatbot_code'))->user_id ?? null;

        // For Chat Response
        if (! is_null(request('provider'))) {
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'chatbot');
            $this->aiProviderModel = request('model');
            manageProviderValues(request('provider'), 'model', 'chatbot');
            
        }

        // For Embedding Response
        if (! is_null(request('embedding_provider'))) {
            $this->aiEmbeddingProvider = AiProviderManager::isActive(request('embedding_provider'), 'aiembedding');
            $this->aiEmbeddingProviderModel = request('embedding_model');
            manageProviderValues(request('provider'), 'embedding_model', 'aiembedding');
        }
    }

    /**
     * Sets the chat type.
     *
     * @param mixed $type The chat type to set.
     * @return void
     */
    public function setChatType($type): void
    {
        $this->chatType = $type;
    }

     /**
     * Get the chat type.
     *
     * @return mixed The chat type.
     */
    public function getChatType(): mixed
    {
        return $this->chatType;
    }

   /**
     * Get the owner ID of a widget chat bot based on its ID.
     *
     * @param int $id The ID of the chat bot.
     * @return \Modules\OpenAI\Models\ChatBot|null The chat bot model instance or null if not found.
     */
    public function getBotOwnerId($chatbotCode): ?\Modules\OpenAI\Entities\ChatBot
    {
        return ChatBot::with('metas')->where('code', $chatbotCode)
            ->where('type', 'widgetChatBot')
            ->first();
    }

    public function validate($validatedData)
    {
        $this->validatedData = $validatedData;
    }

    /**
     * Get a query builder for EmbededResource with its 'metas' relationship eager loaded.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function model(): \Illuminate\Database\Eloquent\Builder
    {
        return EmbededResource::with('metas');
    }
        
    /**
     * Delete materials and their associated metadata by ID and type.
     *
     * @param int $id The ID of the material.
     * @param string $type The type of the material.
     * @throws Exception If no data is found or an error occurs during deletion.
     * @return JsonResponse
     */
    public function delete($id, $type, $visitorId = null): void
    {
        DB::beginTransaction();
        try {
            // Retrieve the parent material and its children in a single query
            $materials = Archive::with(['metas'])->where(['id' => $id, 'type' => $type])->when($visitorId !== null, function($query) use($visitorId) {
                return $query->whereHas('metas', function ($q) use ($visitorId) {
                    $q->where('key', 'visitor_id')->where('value', $visitorId);
                });
            })->get();

            if ($materials->isEmpty()) {
                throw new Exception(__('No data found.'), Response::HTTP_NOT_FOUND);
            }
            
            $this->deleteMeta($materials);

            $childs = Archive::with(['metas'])->where('parent_id', $id)->get();

            if ($childs->isNotEmpty()) {
                $this->deleteMeta($childs);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete meta data for each item in the provided array of items.
     *
     * @param array $items An array of items for which to delete meta data.
     */
    private function deleteMeta($items): void
    {
        // Iterate through each chatBot to unset meta data and save
        foreach ($items as $item) {
            $item->unsetMeta(array_keys($item->getMeta()->toArray()));
            $item->save();
            $item->delete();
        }
    }

    /**
     * Get texts from IDs.
     *
     * @param  array  $ids
     * @return mixed
     */
    public function getTextsFromIds(array $ids)
    {
        $texts = $this->model()->whereIn('id', $ids)->get()->toArray();
    
        $textsById = [];

        foreach ($texts as $text) {
            $textsById[$text['id']] = $text['content'];
        }

        $textsOrderedByIds = [];

        foreach ($ids as $id) {
            if (isset($textsById[$id])) {
                $textsOrderedByIds[] = $textsById[$id];
            }
        }
        $mergedArray = implode(' ', $textsOrderedByIds);
        $this->validatedData['content'] = $mergedArray;
        $this->validatedData['model'] = $this->aiProviderModel;
        $this->validatedData['language'] = request('language');
        $result = $this->aiProvider->askQuestionToContent($this->validatedData);

        $userId = (new ContentService())->getCurrentMemberUserId('meta', null, ['user_id' => $this->userId]);
        handleSubscriptionAndCredit(subscription('getUserSubscription', $userId), 'word', $result->words(), $userId, ['owner_id' => $this->userId]);
        
        return $this->storeInfo($result);
    }

    /**
     * Store data and create records in database
     *
     * @param  mixed  $result
     * @return array|string
     */
    public function storeInfo($result): array|string
    {
        DB::beginTransaction();

        try {
            if (empty(request('chatbot_chat_id'))) {
                $chat = $this->createNewChat();
                $this->createUserReply($chat->id);
                $botReply = $this->createBotReply($result, $chat->id);
            } else {
                $this->createUserReply(request('chatbot_chat_id'));
                $botReply = $this->createBotReply($result, request('chatbot_chat_id'));
            }

            DB::commit();

            return $botReply;
        } catch (Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }
    }

    /**
     * Creates a new chat record.
     *
     * @return Archive The newly created chat instance.
     */
    protected function createNewChat()
    {
        $chat = new Archive();
        $chat->title = request('prompt');
        $chat->unique_identifier = \Str::uuid();
        request('visitor_id') ? $chat->visitor_id = request('visitor_id') : $chat->user_id = auth('api')->user()?->id;
        $chat->provider = request('provider') ?? 'OpenAi'; // dynamically obtained in future.
        $chat->type = $this->chatType; //'chatbot_chat';

        $chat->chat_model = $this->aiProviderModel;
        $chat->embedding_provider = request('embedding_provider');
        $chat->embedding_model = $this->aiEmbeddingProviderModel;
        $chat->chatbot_code = request('chatbot_code');
        $chat->save();

        return $chat;
    }

    /**
     * Retrieve the chat type(s) based on provided file IDs.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of chat types.
     */
    protected function chatType(): \Illuminate\Database\Eloquent\Collection
    {
       return $this->model()->whereIn('id', request('file_id'))->get('type');
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
        request('visitor_id') ? $userReply->visitor_id = request('visitor_id') : $userReply->user_id = auth('api')->user()?->id;
        $userReply->type = "visitor_reply";
        $userReply->visitor_reply = request('prompt');
        $userReply->save();

        return $userReply;
    }

    /**
     * Creates a bot reply record for the specified parent chat.
     *
     * @param  mixed  $result  The result object containing bot response data.
     * @param  int  $parentId  The ID of the parent chat.
     * 
     * @return Archive The newly created bot reply instance.
     */
    protected function createBotReply($result, $parentId)
    {
        $botReply = new Archive();
        $botReply->parent_id = $parentId;
        $botReply->raw_response = json_encode($result);
        $botReply->provider = request('provider') ?? 'OpenAi'; // dynamically obtained when settings are done.
        $botReply->expense = $result->expense;
        if (request('visitor_id')) {
            $botReply->visitor_id = request('visitor_id');
        }
        $botReply->expense_type = 'token'; // dynamically obtained when settings are done.
        $botReply->type = "chatbot_reply";
        $botReply->chatbot_code = request('chatbot_code');
        $botReply->chatbot_reply = $result->content;
        $botReply->total_words = $result->word;

        $botReply->chat_model = $this->aiProviderModel;
        $botReply->embedding_provider = request('embedding_provider');
        $botReply->embedding_model = $this->aiEmbeddingProviderModel;
        $botReply->save();
        return $botReply;
    }

    /**
     * Retrieve the most similar vectors for a given vector.
     *
     * @param array $vector The vector for which to find similar vectors.
     * @param array|null $file_id The file ID to filter the vectors.
     * @param int $limit The maximum number of similar vectors to retrieve.
     * 
     * @return array An array containing the most similar vectors.
     */
    public function getMostSimilarVectors(array $vector, $file_id = null, int $limit = 10)
    {
        $embededFiles = $this->model()->whereNotNull('vector');
        $vectors = $embededFiles->whereIn('parent_id', $file_id)->get()
            ->map(function ($vector) {
                return [
                    'id' => $vector->id,
                    'vector' => json_decode($vector->vector, true),
                ];
            })
            ->toArray();

        $similarVectors = [];
        foreach ($vectors as $v) {
            $cosineSimilarity = $this->calculateCosineSimilarity($vector, $v['vector']);
            $similarVectors[] = [
                'id' => $v['id'],
                'similarity' => $cosineSimilarity,
            ];
        }

        usort($similarVectors, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        return $this->getTextsFromIds(array_column(array_slice($similarVectors, 0, $limit), 'id'));
    }

    /**
     * Calculate the cosine similarity between two vectors.
     */
    private function calculateCosineSimilarity(array $vector1, array $vector2): float
    {
        $dotProduct = 0;
        $vector1Normalization = 0;
        $vector2Normalization = 0;
    
        foreach ($vector1 as $i => $value) {
            $dotProduct += $value * $vector2[$i];
            $vector1Normalization += $value * $value;
            $vector2Normalization += $vector2[$i] * $vector2[$i];
        }
    
        $vector1Normalization = sqrt($vector1Normalization);
        $vector2Normalization = sqrt($vector2Normalization);
    
        return $dotProduct / ($vector1Normalization * $vector2Normalization);
    }

     /**
     * Ask a question and generate a response.
     *
     * @return mixed The generated response.
     */
    public function askQuestion()
    {
        if (! $this->aiProvider) {
            throw new Exception(__(':x provider is not available for the :y. Please contact the administration for further assistance.', ['x' => request('provider'), 'y' => __('Chatbot')]));
        }

        if (! $this->aiEmbeddingProvider) {
            throw new Exception(__(':x provider is not available for the :y. Please contact the administration for further assistance.', ['x' => request('embedding_provider'), 'y' => __('Ai Embedding')]));
        }

        $options['model'] = $this->aiEmbeddingProviderModel;
        $options['text'] = $this->validatedData['prompt'];

        $vector =  $this->aiEmbeddingProvider->createEmbeddings($options);
        $userId = (new ContentService())->getCurrentMemberUserId('meta', null, ['user_id' => $this->userId]);
        handleSubscriptionAndCredit(subscription('getUserSubscription', $userId), 'word', subscription('tokenToWord', $vector->expense()), $userId, ['owner_id' => $this->userId]);

        if (filled($vector->content())) {
            $chatbotCode = request('chatbot_code');
            $embededFiles = $this->model()->whereNull('vector')->whereHas('metas', function ($query) use ($chatbotCode) {
                $query->where(function ($subQuery) use ($chatbotCode) {
                    $subQuery->where('key', 'chatbot_code')
                             ->where('value', $chatbotCode);
                });
            })->pluck('id')->toArray();

            return $this->getMostSimilarVectors($vector->content(), $embededFiles, $this->chunkSize());
        }

        return false;
    }

    /**
     * Retrieve the chunk size for processing.
     *
     * @return int The chunk size.
     */
    public function chunkSize()
    {
        return filled(request('chunk')) ? request('chunk') : $this->chunkData;
    }

    /**
     * Check the existence of data.
     *
     * @param mixed $id The ID of the data to check.
     *
     * @return mixed The existing data.
     */
    public function checkExistance($id)
    {
        return $this->model()->where(['id' => $id])->firstOrFail();
    }

    /**
     * Generate a unique visitor ID.
     *
     * @return string The unique visitor ID.
     */
    public function createVisitorId()
    {
        do {
            $id = 'V' . round(microtime(true) * 1000);
            $archiveExists = Archive::whereHas('metas', function($query) use($id) {
                $query->where('key', 'visitor_id')->where('value', $id);
            })->exists();
        } while ($archiveExists);

        return $id;
    }

    /**
     * Checks if a conversation with the given ID exists.
     *
     * @param string $materials The ID of the conversation to check.
     *
     * @return bool True if the conversation exists, false otherwise.
     */
    public function checkConversationExists($chatId)
    {
        return Archive::where('id', $chatId)->exists();
    }

}
