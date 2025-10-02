<?php

/**
 * @package VisionService
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <[shakib.techvill@gmail.com]>
 * @created 03-02-2024
 */
namespace Modules\OpenAI\Services\v2;

use Modules\OpenAI\Transformers\Api\v2\{
    ChatResource,
    BotReplyResource
};

use Modules\OpenAI\Entities\{
    Archive, 
    ChatBot
};
use Illuminate\Http\Response;
use Exception, Str, DB, AiProviderManager;
use Modules\OpenAI\Services\v2\ArchiveService;
use Modules\OpenAI\Services\ContentService;

class VisionService
{
    private $aiProvider;

    /**
     * Method __construct
     *
     *
     * @return void
     */
    public function __construct() 
    {
        if(! is_null(request('provider'))) {
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'visionchat');
            manageProviderValues(request('provider'), 'model', 'visionchat');
            
        }
    }
 
     /**
      * Create a new chat conversation.
      *
      * @param  array  $requestData  The data for the chat conversation.
      * @return BotReplyResource  An array containing the bot's reply.
      * @throws \Exception
      */
    public function store(array $requestData): BotReplyResource
    {
        if (! $this->aiProvider) {
            throw new Exception(__('Provider not found.'));
        }

        try {
            $visionChat = $this->aiProvider->visionChat($requestData);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }

        $content = $visionChat->content();
        $words = $visionChat->words();
        $expense = $visionChat->expense();
        $response = $visionChat->response(); 
        $provider = request('provider');
        $model = request('model');

        $chatBot = isset($requestData['botId']) ? $this->chatBot($requestData['botId']) : $this->chatBot();

        $chat = isset($requestData['chatId']) ? Archive::where(['id' =>$requestData['chatId'], 'type' => 'vision_chat'])->first() : null;

        DB::beginTransaction();
        try {

            if (! empty($content)) {

                // Update the database based on whether the chat already exists or not
                if (! $chat) {
                    // Create a new chat record
                    $chat = ArchiveService::create([
                        'user_id'=> auth()->id(),
                        'title'=> filteringBadWords($requestData['prompt']),
                        'unique_identifier' => (string) Str::uuid(),
                        'provider'=> $provider,
                        'model'=> $model,
                        'expense' => $expense->totalTokens,
                        'type' => 'vision_chat',
                        'total_words' => $words
                    ]);

                    // Create user reply record
                    $data = [
                        'parent_id'=> $chat->id,
                        'user_id'=> auth()->id(),
                        'type' => 'vision_chat_reply',
                        'total_words' => $words,
                        'user_reply'=> filteringBadWords($requestData['prompt']),

                    ];

                    if (isset($requestData['file'])) {
                        $data['user_files'] = $this->prepareFiles($requestData['file']);
                    }

                    ArchiveService::create($data);

                    // Create bot reply record
                    $botReply = ArchiveService::create([
                        'parent_id'=> $chat->id,
                        'raw_response'=> json_encode($response),
                        'provider'=> $provider,
                        'model'=> $model,
                        'expense' => $expense->totalTokens,
                        'type' => 'vision_chat_reply',
                        'bot_id' => $chatBot->id,
                        'bot_reply' => $content,
                        'total_words' => $words,
                        'prompt_tokens' => $expense->promptTokens,
                        'completion_tokens' => $expense->completionTokens
                    ]);

                } else {

                    // Update existing chat with user and bot replies

                    $data = [
                        'parent_id'=> $chat->id,
                        'user_id'=> auth()->id(),
                        'type' => 'vision_chat_reply',
                        'user_reply'=> filteringBadWords($requestData['prompt']),

                    ];

                    if (isset($requestData['file'])) {
                        $data['user_files'] = $this->prepareFiles($requestData['file']);
                    }

                    ArchiveService::create($data);

                    // Bot Reply
                    $botReply = ArchiveService::create([
                        'parent_id'=> $chat->id,
                        'raw_response'=> json_encode($response),
                        'provider'=> $provider,
                        'model'=> $model,
                        'expense' => $expense->totalTokens,
                        'type' => 'vision_chat_reply',
                        'bot_id' => $chatBot->id,
                        'bot_reply' => $content,
                        'total_words' => $words,
                        'prompt_tokens' => $expense->promptTokens,
                        'completion_tokens' => $expense->completionTokens
                    ]);
                }

                $userId = (new ContentService())->getCurrentMemberUserId('meta', null);
                // Update Subscription and Credit.
                handleSubscriptionAndCredit(subscription('getUserSubscription', $userId), 'word', $words, $userId);
                DB::commit();
                return new BotReplyResource($botReply);
            } else {
                throw new Exception(__("Unable to connect with the bot. Please try again."), Response::HTTP_INTERNAL_SERVER_ERROR);
            }

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }
 
    /**
     * Create upload path
     * @return [type]
     */
    protected function uploadPath()
	{
		return createDirectory(join(DIRECTORY_SEPARATOR, ['public', 'uploads','aiVision']));
	}

    /**
     * prepare file
     * @return [type]
     */

    public function storeFiles($data)
    {
        $this->uploadPath();

        $clientExtention = $data->getClientOriginalExtension();
        $fileName = md5(uniqid()) . "." . $clientExtention;
        $destinationFolder = 'public' . DIRECTORY_SEPARATOR . 'uploads'. DIRECTORY_SEPARATOR . 'aiVision'. DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;
        
        if (!isExistFile($destinationFolder)) {
            createDirectory($destinationFolder);
        }

        $filePath = $destinationFolder . $fileName;
        objectStorage()->put($filePath, file_get_contents($data->getRealPath()));

        return $filePath;
    }

    /**
     * Prepares files for storage.
     *
     * @param array $files
     * @return array
     */
    protected function prepareFiles(array $files): array
    {
        $data = [];

        foreach ($files as $file) {
            $data[] = $this->storeFiles($file);
        }

        return $data;
    }

    /**
      * Create a new chat conversation.
      *
      * @param  array  $requestData  The data for the chat conversation.
      *
      * @return ChatResource  An array containing the bot's reply.
      * @throws \Exception
      *
      */
    public function update(array $requestData): ChatResource
    {
        DB::beginTransaction();
        try {
            $visionChat = Archive::where(['id' => $requestData['chatId'], 'type' => 'vision_chat'])->first();

            if (!empty($visionChat)) {
                $visionChat->title = $requestData['name'];
                $visionChat->save();

                DB::commit();
                return new ChatResource($visionChat);
            } else {
                throw new Exception( __('The :x does not exist.', ['x' => __('Vision Conversation')]), Response::HTTP_NOT_FOUND);
            }
                
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
      * Delete a chat conversation with chats.
      *
      * @param  int  $chatId  The data for the chat conversation.
      *
      * @return bool
      * @throws \Exception
      *
      */
      public function delete(int $chatId): bool
      {
          DB::beginTransaction();
          try {
                $chat = Archive::where(['id' => $chatId, 'type' => 'vision_chat'])->first()
                        ?? throw new Exception(__(':x does not exist.', ['x' => __('Vision Chat')]), Response::HTTP_NOT_FOUND);

                $chat->unsetMeta(['total_words']);
                $chat->save();
                $chat->delete();
           
                $chatReplies = Archive::with('metas')->where(['parent_id' => $chatId, 'type' => 'vision_chat_reply'])->get();
                if (! $chatReplies->isEmpty()) {
                    foreach ($chatReplies as $reply) {
                        // Remove specified metas and save changes
                        $reply->unsetMeta(['user_reply', 'user_files', 'bot_id', 'bot_reply', 'total_words', 'prompt_tokens', 'completion_tokens']);
                        $reply->save();
                        $reply->delete();
                    }
                }
  
                DB::commit();
                return true;
                  
          } catch (Exception $e) {
              DB::rollBack();
              throw new Exception($e->getMessage(), $e->getCode());
          }
      }
  

    /**
      * Retrieve a vision chat bot by ID
      *
      * @param  int|null  $chatBotId
      * @return ChatBot
      */
      public function chatBot(int $chatBotId = null): ChatBot
      {
          $chatBot = ChatBot::query();
          if ($chatBotId) {
              return $chatBot->where(['id' => $chatBotId, 'status' => 'Active'])->first();
          }
          return $chatBot->where(['status' => 'Active', 'type' => 'vision'])->first();
      }
}
