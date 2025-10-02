<?php

namespace Modules\OpenAI\Services\v2;

use Modules\OpenAI\Transformers\Api\v2\ChatbotWidget\ChatBotWidgetResource;
use Modules\OpenAI\Entities\{
    ChatBot,
    Archive,
    EmbededResource,
    FeaturePreference
};

use App\Models\File;
use Exception, DB, App;
use Illuminate\Http\Response;
use App\Traits\ReportHelperTrait;
use Modules\OpenAI\Services\ContentService;
use Modules\MediaManager\Http\Models\ObjectFile;

class ChatBotWidgetService
{
    use ReportHelperTrait;

    private $contentService;

    /**
     * ChatBotWidgetService constructor.
     *
     */
    public function __construct()
    {
        $this->contentService = new ContentService();
    }

    /**
      * Create a new chat bot
      *
      * @param array $requestData
      *
      * @return ChatBotWidgetResource
      * @throws \Exception
      * 
      */
    public function store(array $requestData)
    {
        /******creation chatbot **/
        $subscription = null;
        $userId = $this->contentService->getCurrentMemberUserId('meta', null);

        if (! subscription('isAdminSubscribed')) {
            $userStatus = $this->contentService->checkUserStatus($userId, 'meta');
            if ($userStatus['status'] == 'fail') {
                throw new Exception($userStatus['message'], Response::HTTP_FORBIDDEN);
            }

            $validation = subscription('isValidSubscription', $userId, 'chatbot', null, null);
            $subscription = subscription('getUserSubscription', $userId);
            if ($validation['status'] == 'fail' && ! auth()->user()->hasCredit('chatbot')) {
                throw new Exception($validation['message'], Response::HTTP_FORBIDDEN);
            }
        }

        DB::beginTransaction();

        try {
            // Store New Chat Bot
            $newChatBot = new ChatBot();
            $newChatBot->user_id = auth('api')->user()->id;
            $newChatBot->name =  $requestData['name'];
            $newChatBot->code = substr(str_replace('-', '', (string) \Str::uuid()), 0, 15);
            $newChatBot->message ="Hey, my name is " . $requestData['name'] . ". How can I help you today?";
            $newChatBot->description = "What brings you here today? Feel free to ask anything.";
            $newChatBot->role = "Ai Assistant";
            $newChatBot->status = "Active";
            $newChatBot->type = 'widgetChatBot';
            $newChatBot->is_default = 0;
            $newChatBot->theme_color = $requestData['theme_color'];
            $newChatBot->language = 'English';
            $newChatBot->brand = (boolean) true;

            // Chat Provider & Model
            $newChatBot->provider = $requestData['provider'];
            $newChatBot->model = $requestData['model'];

            // Chat Provider & Model
            $newChatBot->embedding_provider = $requestData['embedding_provider'];
            $newChatBot->embedding_model = $requestData['embedding_model'];

            $newChatBot->image = [
               'url' => $this->chatbotSettings('image'),
               'is_delete' => false
            ];

            $newChatBot->floating_image = [
                'url' => $this->chatbotSettings('floating_image'),
                'is_delete' => false
            ];

            // NOTE:: will be dynamic
            $newChatBot->script_code = "<script src='" . url('/') . "/Modules/Chatbot/Resources/assets/js/chatbot-widget.min.js'  data-iframe-src=\"" . url("/chatbot/embed/chatbot_code={$newChatBot->code}/welcome") . "\" data-iframe-height=\"532\" data-iframe-width=\"400\"></script>";

            $newChatBot->save();

            handleSubscriptionAndCredit(subscription('getUserSubscription',$userId), 'chatbot', 1, $userId);
            
            DB::commit();
            return new ChatBotWidgetResource($newChatBot);

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Update a widget chat bot with the provided data.
     *
     * @param $code The unique code of the ChatBot to update.
     * @param array $requestData The data to update the chat bot with.
     *
     * @return ChatBotWidgetResource The updated chat bot resource.
     * @throws \Exception If the chat bot does not exist or an error occurs during the update.
     */
    public function update(string $code, array $requestData): ChatBotWidgetResource
    {
        DB::beginTransaction();

        try {
            $chatBot = ChatBot::where(['code' => $code, 'type' => 'widgetChatBot', 'user_id' =>auth()->user()->id])->first();

            if ($chatBot) {
                if (request()->hasFile('image')) {
                    $chatBot->updateSingleFile('image', ['isUploaded' => false,'isSavedInObjectFiles' => true, 'isOriginalNameRequired' => true, 'thumbnail' => false]);

                    $this->updateStorageData($chatBot);

                    $imageUrl = str_replace(objectStorage()->url('/'), '', $chatBot->fileUrl());

                    $chatBot->image = [
                        'url' => $imageUrl,
                        'is_delete' => true,
                    ];

                    unset($requestData['image']);
                }

                if (request()->hasFile('floating_image')) {
                    $floatingImage = $this->upload(request()->file('floating_image'));
                    $chatBot->floating_image  = [
                        'url' => $floatingImage,
                        'is_delete' => true,
                    ];

                    unset($requestData['floating_image']);
                }

                // Remove _method from requestData
                unset($requestData['_method']);

                // Update other fields from requestData
                foreach ($requestData as $key => $field) {
                    if (isset($field) && !empty($field)) {
                        $value = $field === 'false' ? (boolean) false :  $field;
                        $chatBot->$key = $value;
                    }
                }

                $chatBot->save();
                DB::commit();

                return new ChatBotWidgetResource($chatBot);
            } else {
                throw new Exception(__('The :x does not exist.', ['x' => __('Chatbot')]), Response::HTTP_NOT_FOUND);
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        

    }

    /**
     * Delete a chat bot by its code.
     *
     * @param string $code The code of the chat bot to be deleted.
     *
     * @throws \Exception If the chat bot does not exist.
     */
    public function delete($code)
    {
        DB::beginTransaction();
        try {

            $chatBot = ChatBot::whereType('widgetChatBot')->whereCode($code)->where('user_id', auth()->user()->id)->first();

            if ($chatBot) {
                $chatBot->unsetMeta(array_keys($chatBot->getMeta()->toArray()));
                $chatBot->save();

                $chatBot->delete() ?: throw new Exception(__('Something went wrong, please try again.'));

                // Delete Chatbot's all materials
                $this->deleteMaterials($code);

                DB::commit();
            } else {
                throw new Exception(__(':x does not exist.', ['x' => __('Chatbot')]), Response::HTTP_NOT_FOUND);
            }

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete materials related to a specific chat bot code.
     *
     * @param string $code The code of the chat bot for which materials should be deleted.
     *
     * @return void
     */
    public function deleteMaterials(string $code): void
    {

        $resources = EmbededResource::with(['metas', 'user', 'childs'])->whereCategory('widgetChatBot')
                ->whereHas('metas', function ($q) use ($code) {
                    $q->where('key', 'chatbot_code')->where('value', $code);
                })->get();

        $this->deleteMeta($resources);

    }

    /**
     * Delete meta data for each item in the provided array of items.
     *
     * @param array|object $items An array of items for which to delete meta data.
     *
     * @return void
     */
    private function deleteMeta(array|object $items): void
    {
        // Iterate through each chatBot to unset meta data and save
        foreach ($items as $item) {

            if ($item->childs && !$item->childs->isEmpty()) {
                $this->deleteMeta($item->childs);
            }
            
            $item->unsetMeta(array_keys($item->getMeta()->toArray()));
            $item->save();
            $item->delete();
        }
    }

    /**
      * Update Storage Driver data
      *
      * @param array $requestData
      *
      * @throws \Exception
      *
      */
    private function updateStorageData($object) {

        $id = $object->objectFile()->value('file_id');
        if ($id) {
            $file = App\Models\File::where([ 'id' => $id])->first();

            $currentValue = app()->make('all-image');
            $newValue = 'public/uploads/'. str_replace('\\', '/', $file->file_name);
            
            if (is_array($currentValue)) {
                $currentValue[] = $newValue;
            }
            app()->instance('all-image', $currentValue);
        }
    }

    /**
     * Create upload path
     * @return [type]
     */
    public function uploadPath()
	{
		return createDirectory(join(DIRECTORY_SEPARATOR, ['public', 'uploads']));
	}

    /**
     * Store Images
     * @param mixed $data
     *
     * @return [type]
     */
    public function upload($file)
    {
        $filename = md5(uniqid()) . "." . $file->getClientOriginalExtension();
        objectStorage()->put($this->uploadPath() . DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR . $filename, file_get_contents($file));
        $this->makeThumbnail($filename);
        return $this->uploadPath() . DIRECTORY_SEPARATOR .  date('Ymd') . DIRECTORY_SEPARATOR . $filename;
    }
    
    /**
     * make thumbnail
     * @param  int  $id
     * @return boolean
     */
    public function makeThumbnail($uploadedFileName)
    {
        $uploadedFilePath = objectStorage()->url($this->uploadPath());
        $thumbnailPath = createDirectory($this->uploadPath());
        (new File)->resizeImageThumbnail($uploadedFilePath, $uploadedFileName, $thumbnailPath);
        return true;
    }

    /**
     * Delete the image associated with a chatbot and update its image attribute.
     *
     * @param string $code The code of the chatbot.
     * @return ChatBot The updated ChatBot model instance.
     * @throws \Exception If the chatbot or its associated image file is not found,
     *                    or if an error occurs during file deletion or database operations.
     */
    public function deleteImage(string $code, array $requestData): ChatBot
    {
        DB::beginTransaction();

        try {
            $chatBot = ChatBot::where(['type' => 'widgetChatBot', 'code' => $code, 'user_id' => auth()->user()->id])->first();

            if (!$chatBot) {
                throw new Exception(__('The :x does not exist.', ['x' => __('Chatbot')]), Response::HTTP_NOT_FOUND);
            }

            if (isset($requestData['name']) && !empty($requestData['name'])) {

                if (isset($chatBot->{$requestData['name']}) && !empty($chatBot->{$requestData['name']})) {
                    $chatBot->{$requestData['name']} = [
                        'url' => $this->chatbotSettings($requestData['name']),
                        'is_delete' => false
                    ];
                
                    $chatBot->save();
                    DB::commit();
            
                    return $chatBot;
                } else {
                    throw new Exception(__("The ':x' image was not found.", ['x' => $requestData['name']]));
                }

                
            }

            $fileId = $chatBot->objectFile()->value('file_id');
            $file = File::find($fileId);

            if ($file && isExistFile('public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file->file_name)) {
                objectStorage()->delete('public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file->file_name);
                ObjectFile::where('file_id', $file->id)->delete();

                if (!$file->delete()) {
                    throw new Exception(__('Something went wrong, please try again.'));
                }

                $chatBot->image = [
                    'url' => $this->chatbotSettings('image'),
                    'is_delete' => false
                ];

                $chatBot->save();

                $this->removeThumbnail($file);
                
                DB::commit();

                return $chatBot;
            } else {
                throw new Exception(__('The :x image was not found.', ['x' => __('Chatbot\'s')]));
            }

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Remove thumbnail if exist
     *
     * @param object $image
     * @return void
     */
    private function removeThumbnail($image)
    {
        $sizes = array_keys((new File)->sizeRatio());

        foreach ($sizes as $value) {
            if (isExistFile($this->getThumbnailPath($value, $image))) {
                objectStorage()->delete($this->getThumbnailPath($value, $image));
            }
        }
    }

    /**
     * Get thumbnail path
     *
     * @param string $size
     * @param object $image
     * @return string
     */
    private function getThumbnailPath($size, $image)
    {
        return implode(DIRECTORY_SEPARATOR, ['public', 'uploads', config('openAI.thumbnail_dir'), $size, $image->file_name]);
    }
    
    /**
     * Retrieve and process data for the dashboard.
     *
     * @return array
     */
    public function dashboardData(): array
    {
       
        $allActiveBots = ChatBot::where(['type' => 'widgetChatBot', 'status' => 'Active', 'user_id' => auth()->user()->id])->get();
        $totalActiveBots = $allActiveBots->count();

        // Extract IDs of active bots
        $ids = $allActiveBots->pluck('code');
        // Initialize an array to store conversation IDs
        $conversationIds = [];

        // Fetch conversation IDs for each bot ID
        foreach ($ids as $id) {
            $chatBot = Archive::whereType('chatbot_chat')
                ->whereHas('metas', function ($query) use ($id) {
                    $query->where(['key' => 'chatbot_code', 'value' => $id]);
                })
                ->pluck('id')
                ->toArray();

            // Merge conversation IDs into the main array
            $conversationIds = array_merge($conversationIds, $chatBot);
        }

        // Remove duplicate conversation IDs if needed
        $conversationIds = array_unique($conversationIds);

        // Get the count of unique conversation IDs
        $totalConversation = count($conversationIds);

        // For Chart
        $startDate = $this->offsetDate('-30');
        $endDate = $this->tomorrow();

        // Get the archives with necessary relationships and conditions
        $archives = Archive::with('metas')
            ->where('type', 'chatbot_chat')
            ->whereHas('metas', function ($query) use ($ids) {
                $query->where('key', 'chatbot_code')
                      ->whereIn('value', $ids);
            })
            ->whereHas('metas', function ($query) {
                $query->where('key', 'visitor_id');
            })
            ->get()
            ->unique(function ($archive) {
                return $archive->metas->where('key', 'visitor_id')->pluck('value');
            });
        
        // Overall visitiors
        $totalVisitor =  $archives->count();

        $archives = $archives->whereBetween('created_at', [$startDate, $endDate]);

        // Group archives by date string (Y-m-d) and count unique visitors per day
        $visitorCounts = $archives->groupBy(function ($archive) {
            return $archive->created_at->format('F j, Y');
        })->map(function ($group) {
            return $group->pluck('metas')->count();
        })->toArray();

        // Initialize $values array with dates as keys and default 0 counts
        $values = [];
        $currentDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        while ($currentDate < $endDate) {
            $date = $currentDate->format('F j, Y');
            $values[formatDate($date)] = 0;
            $currentDate->modify('+1 day');
        }

        // Assign visitor counts to the correct date in $values array
        foreach ($visitorCounts as $day => $count) {
            $values[$day] = $count;
        }

        return [
            'total_active_bot' => $totalActiveBots,
            'total_conversation' => $totalConversation,
            'total_users' => $totalVisitor,
            'users_per_day' => $values
        ];
    }

    /**
     * Retrieve the chatbot settings file URL based on the feature preference.
     *
     * @return string The URL of the chatbot settings file.
     */
    public function chatbotSettings($option)
    {
        $keys = [
            'image' => 'default_avatar',
            'floating_image' => 'default_floating_image',
        ];

        $preference = FeaturePreference::whereSlug('chatbot')->first();

        if (!$preference || !isset($preference->general_options)) {
            return null;
        }

        $data = json_decode($preference->general_options, true);

        $keysToUpdate = [
            'default_avatar' => defaultImage('chatbots'), 
            'default_floating_image' => defaultImage('chatbot_floating_image')
        ];

        $fileIds = array_intersect_key($data, $keysToUpdate);
        
        if (!empty($fileIds)) {
            $files = \App\Models\File::whereIn('id', $fileIds)->get()->keyBy('id');
            
            foreach ($keysToUpdate as $key => $defaultImage) {
                if (isset($data[$key])) {
                    $file = $files->get($data[$key]);

                    $fileUrl = $file ? $file->file_name : $defaultImage;
                    $data[$key] = $this->uploadPath() . '//' . $fileUrl;
                }
            }
        }

        return $data[$keys[$option]] ?? null;
    }
}
