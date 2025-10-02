<?php 

namespace Modules\OpenAI\Services\v2;

use Illuminate\Support\Facades\DB;
use Modules\OpenAI\Services\ContentService;
use Modules\OpenAI\Entities\{
    EmbededResource,
    Archive
};
use Exception;
use AiProviderManager;


class DocChatAskService
{
    /**
     * @var string The type of chat, default chat is doc_chat.
     */
    private $chatType = 'doc_chat';
    private $aiProvider;
    private $aiEmbeddingProvider;

    /**
     * @var int The size of the chunk, default value is 256.
     */
    protected $chunkSize = 256;

    /**
     * Constructor method.
     *
     *
     * @return void
     */
    public function __construct()
    {
        if(! is_null(request('provider'))) {
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'aidocchat');
            manageProviderValues(request('provider'), 'model', 'aidocchat');
        }

        if(! is_null(request('embedding_provider'))) {
            $this->aiEmbeddingProvider = AiProviderManager::isActive(request('embedding_provider'), 'aiembedding');
            manageProviderValues(request('embedding_provider'), 'embedding_model', 'aiembedding');
        }
    }

    /**
     * Create upload path.
     *
     * @return string The generated upload path.
     */
    protected function uploadPath()
    {
        return createDirectory(implode(DIRECTORY_SEPARATOR, ['public', 'uploads', 'aiFiles']));
    }

   /**
     * Store file.
     *
     * @param mixed $file The file to be stored.
     *
     * @return array Information about the stored file.
     */
    public function storeFile($file)
    {
        $this->uploadPath();
        $fileName = md5(uniqid()) . '.' . $file->getClientOriginalExtension();

        // Create a destination path
        $destinationFolder = public_path('uploads' . DIRECTORY_SEPARATOR . 'aiFiles' . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR);

        // Ensure the directory exists
        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0755, true);
        }

        $fileSize = $file->getSize();
        
        // Get the file content
        $fileContent = file_get_contents($file->getRealPath());
        
        // Save the file content to the destination
        file_put_contents($destinationFolder . $fileName, $fileContent);

        $path = date('Ymd') . DIRECTORY_SEPARATOR . $fileName;

        return [
            'path' => $path,
            'destinationPath' => $destinationFolder . $fileName,
            'extension' => $file->getClientOriginalExtension(),
            'name' => $fileName,
            'file_path_name' => date('Ymd') . DIRECTORY_SEPARATOR . $fileName,
            'originalName' => $file->getClientOriginalName(),
            'fileSize' => $fileSize,
        ];

    }

    /**
     * Ask a question and generate a response.
     *
     * @return mixed The generated response.
     */
    public function askQuestion(array $requestData)
    {
        if (! $this->aiProvider) {
            throw new Exception(__(':x provider is not available for the :y. Please contact the administration for further assistance.', ['x' => ucfirst(request('provider')), 'y' => __('Ai Doc Chat')]));
        }

        if (! $this->aiEmbeddingProvider) {
            throw new Exception(__(':x provider is not available for the :y. Please contact the administration for further assistance.', ['x' => ucfirst(request('embedding_provider')), 'y' => __('Ai Embedding')]));
        }

        $options['text'] = $requestData['prompt'];
        $options['model'] =  request('embedding_model');
        $userId = (new ContentService())->getCurrentMemberUserId('meta', null);

        $vector = $this->aiEmbeddingProvider->createEmbeddings($options);
        $usages = subscription('tokenToWord', $vector->expense());
        handleSubscriptionAndCredit(subscription('getUserSubscription', $userId), 'word', $usages, $userId);

        if (filled($vector->content())) {
            return $this->getMostSimilarVectors($vector->content(), $requestData['file_id'], $this->chunkSize);
        }

        return false;
    }

    /**
     * Get texts from IDs.
     *
     * @param  array  $ids
     * @return mixed
     */
    public function getTextsFromIds(array $ids)
    {
        $texts = EmbededResource::with('metas')->whereIn('id', $ids)->get()->toArray();
        $this->chatType = $texts[0]['type'];

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

        $options['prompt'] = request('prompt');
        $options['chat_model'] = request('model');
        $options['context'] = $mergedArray;
        $options['temperature'] = request('temperature');
        $options['language'] = request('language');
        $options['tone'] = request('tone');

        $result = $this->aiProvider->askQuestion($options);

        if ($result->expense()) {
            $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
            handleSubscriptionAndCredit(subscription('getUserSubscription', $userId), 'word', $result->words(), $userId);
        }

        return $this->storeInfo($result);
    }

    /**
     * Retrieve the most similar vectors for a given vector.
     *
     * @param array $vector The vector for which to find similar vectors.
     * @param int|null $file_id The file ID to filter the vectors.
     * @param int $limit The maximum number of similar vectors to retrieve.
     *
     * @return array An array containing the most similar vectors.
     */
    public function getMostSimilarVectors(array $vector, $file_id = null, int $limit = 10)
    {
        $embededFiles = EmbededResource::with('metas');

        if (!is_null($file_id)) {
            $embededFiles = $embededFiles->whereIn('id', $file_id)->orWhere('parent_id', $file_id);
        }
        
        $vectors = $embededFiles->get()
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
     * Store data and create records in database
     *
     * @param  mixed  $result
     * @return array|string
     */
    public function storeInfo($result): array|string
    {
        DB::beginTransaction();

        try {
            if (empty(request('parent_id'))) {
                $chat = $this->createNewChat();
                $this->createUserReply($chat->id);
                
                $botReply = $this->createBotReply($result, $chat->id);
                
            } else {
                $this->createUserReply(request('parent_id'));
                $botReply = $this->createBotReply($result, request('parent_id'));
            }

            DB::commit();
            return $botReply;
        } catch (Exception $e) {
            DB::rollBack();

            return $e->getMessage();
        }
    }

    /**
     * Retrieves a unique provider and model from the resources.
     *
     *
     * @param array $ids The IDs of the resources to fetch.
     * @return array|null
     */
    public function getProviderModel(array $ids)
    {
        $resources = EmbededResource::with('metas')->whereIn('id', $ids)->get();

        $providers = $resources->pluck('embedding_provider')->unique();
        $models = $resources->pluck('embedding_model')->unique();

        if ($providers->count() === 1 && $models->count() === 1) {
            return [
                'provider' => $providers->first(),
                'model' => $models->first(),
            ];
        }

        return null;
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
        $chat->user_id = auth()->id();

        $chat->provider = request('provider');
        $chat->chat_model = request('model');

        $chat->embedding_provider = request('embedding_provider');
        $chat->embedding_model =  request('embedding_model');
        $chat->type = $this->chatType;
        $chat->save();

        return $chat;
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
        $userReply->type = $this->chatType. "_chat_reply";
        $userReply->user_reply = request('prompt');
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

        $botReply->provider = request('provider');
        $botReply->chat_model = request('model');
        $botReply->expense = $result->expense();
        
        $botReply->expense_type = 'token'; // dynamically obtained

        $botReply->type = $this->chatType . "_chat_reply";
        $botReply->file_id = implode(',', request('file_id'));
        $botReply->bot_reply = $result->content();
        $botReply->total_words = $result->words();
        $botReply->embedding_provider = request('embedding_provider');
        $botReply->embedding_model =  request('embedding_model');

        $botReply->save();
        return $botReply;
    }

}
