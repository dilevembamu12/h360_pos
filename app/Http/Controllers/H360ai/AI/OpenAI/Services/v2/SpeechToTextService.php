<?php
/**
 * @package SpeechToTextService
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <[shakib.techvill@gmail.com]>
 * @created 08-08-2024
 */

namespace Modules\OpenAI\Services\v2;

use Modules\OpenAI\Contracts\Responses\SpeechToText\SpeechResponseContract;
use Modules\OpenAI\Services\ContentService;
use App\Facades\AiProviderManager;
use Modules\OpenAI\Services\v2\ArchiveService;
use Exception;

use App\Models\{
    User,
    Language,
    Team,
    TeamMemberMeta
};

class SpeechToTextService
{
    /**
     * @var \App\Facades\AiProviderManager  The AI provider manager instance.
     */
    private $aiProvider;

    /**
     * @var array  An array to hold data for processing.
     */
    private $data = [];

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
            $this->aiProvider = AiProviderManager::isActive(request('provider'), 'speechtotext');
        }
    }

    /**
     * Handles the generation of speech by processing the provided request data.
     *
     * @param array $requestData The data required for processing the speech request.
     *
     * @throws Exception If the AI provider is not available.
     * @throws Exception If the speech response is not an instance of SpeechResponseContract.
     * @throws Exception If any other exception occurs during the process.
     *
     * @return mixed
     */
    public function handleSpeechGenerate(array $requestData): mixed
    {
        if (! $this->aiProvider) {
            throw new Exception(__('Provider not found.'));
        }

        app('Modules\OpenAI\Http\Requests\SpeechStoreRequest')->safe();
        
        try {
            $result = $this->aiProvider->speechToText($requestData);

            if (! ($result instanceof SpeechResponseContract)) {
                throw new Exception(__('Speech response must be an instance of SpeechResponseContract.'));
            }
        
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        $file = $requestData['file'];

        $this->data = [
            'file_name' => $this->storeFile($file),
            'original_file_name' => $file->getClientOriginalName(),
            'file_size' => $this->getFileSize($file)
        ];

        return $this->storeInfo($result);
    }


    /**
     * Store data and create records in database
     *
     * @param \Modules\OpenAI\Contracts\Responses\SpeechToText\SpeechResponseContract $result
     * @return mixed
     */
    public function storeInfo(SpeechResponseContract $result): mixed
    {
        \DB::beginTransaction();
        try {
            if (empty(request('parent_id'))) {
                $chat = $this->createNewChat();
                $this->createUserReply($chat->id);

                $botReply = $this->createBotReply($chat->id, $result);

            } else {
                $this->createUserReply(request('parent_id'));
                $botReply = $this->createBotReply(request('parent_id'), $result);
            }
            \DB::commit();
            return $botReply;
        } catch (Exception $e) {
            \DB::rollBack();
            return $e->getMessage();
        }
    }

     /**
     * Creates a new chat record.
     *
     * @return \Illuminate\Database\Eloquent\Model The newly created chat instance.
     */
    protected function createNewChat(): \Illuminate\Database\Eloquent\Model
    {
        return ArchiveService::create([
            'unique_identifier' => \Str::uuid(),
            'user_id' => auth()->id(),
            'provider' => request('provider'),
            'type' => 'speech_to_text_chat',
            'language' => request('language'),
            'temperature' => request('temperature'),
            'file_name' => $this->data['file_name'],
            'original_file_name' => $this->data['original_file_name'],
            'file_size' => $this->data['file_size']
        ]);
    }
    /**
     * Creates a user reply record for the specified parent chat.
     *
     * @param  int  $parentId  The ID of the parent chat.
     *
     * @return \Illuminate\Database\Eloquent\Model The newly created user reply instance.
     */
    protected function createUserReply(int $parentId): \Illuminate\Database\Eloquent\Model
    {
        return ArchiveService::create([
            'parent_id' => $parentId,
            'user_id' => auth()->id(),
            'provider' => request('provider'),
            'type' => 'speech_to_text_chat_reply',
            'language' => request('language'),
            'temperature' => request('temperature'),
            'file_name' => $this->data['file_name'],
            'original_file_name' => $this->data['original_file_name'],
            'file_size' => $this->data['file_size']
        ]);
    }
    /**
     * Creates a bot reply record for the specified parent chat.
     *
     * @param  mixed  $result  The result object containing bot response data.
     * @param  int  $parentId  The ID of the parent chat.
     *
     * @return \Illuminate\Database\Eloquent\Model The newly created bot reply instance.
     */
    protected function createBotReply($parentId, $result): \Illuminate\Database\Eloquent\Model
    {
        $text = request('word_filter') === 'Active'  ? filteringBadWords($result->text()) : $result->text();
        $botReply = ArchiveService::create([
            'parent_id' => $parentId,
            'speech_to_text_creator_id' => auth()->id(),
            'title' => $text,
            'content' => $text,
            'provider' => request('provider'),
            'type' => 'speech_to_text_chat_reply',
            'language' => request('language'),
            'temperature' => request('temperature'),
            'file_name' => $this->data['file_name'],
            'original_file_name' => $this->data['original_file_name'],
            'file_size' => $this->data['file_size'],
            'duration' => $result->duration(),
            'balanceReduce' => ! subscription('isAdminSubscribed') || auth()->user()->hasCredit('minute') ? 'subscription' : 'onetime'
        ]);
        
        handleSubscriptionAndCredit(subscription('getUserSubscription', auth()->id()), 'minute', $botReply->duration, auth()->id());
        return $botReply;
    }

    /**
     * Create upload path
     * 
     * @return string
     */
    protected function uploadPath(): string
	{
		return createDirectory(join(DIRECTORY_SEPARATOR, ['public', 'uploads','aiAudios']));
	}

    /**
     * Store an AI-generated audio file.
     *
     * @param \Illuminate\Http\UploadedFile $aiOptions The uploaded file to store.
     * @return string The path where the file is stored.
     */
    public function storeFile($aiOptions): string
    {
        $this->uploadPath();

        $uploadedFile = $aiOptions;
        $fileName = md5(uniqid()) . "." . $uploadedFile->getClientOriginalExtension();
        $destinationFolder = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'aiAudios'. DIRECTORY_SEPARATOR . date('Ymd') . DIRECTORY_SEPARATOR;

        if (!isExistFile($destinationFolder)) {
            createDirectory($destinationFolder);
        }

        objectStorage()->put($destinationFolder . $fileName, file_get_contents($uploadedFile->getRealPath()));

        $path = date('Ymd') . DIRECTORY_SEPARATOR . $fileName;
        return $path;
    }

    /**
     * Gets the size of a file in kilobytes.
     *
     * @param \SplFileInfo $file  The file object.
     * @return float  The size of the file in kilobytes.
     */
    protected function getFileSize(\SplFileInfo $file): float
    {
        $bytes = $file->getSize();  // Use the file's getSize method
        return $bytes !== false ? round($bytes / 1024, 2) : 0.0;
    }

    /**

     * Team member meta insert or update
     * @param array $words
     *
     * @return bool|array
     */
    public function storeTeamMeta($minutes): bool|array
    {
        $memberData = Team::getMember(auth()->user()->id);
        if (!empty($memberData)) {
            $usage = TeamMemberMeta::getMemberMeta($memberData->id, 'minute_used');
            if (!empty($usage)) {
                return $usage && $usage->increment('value', $minutes);
            }
        }
        return false;
    }

    /**
     * Updates the content of a speech record.
     *
     * @param int $id  The ID of the speech record to update.
     * @param string $content  The new content for the speech record.
     * @return \Illuminate\Http\JsonResponse  The JSON response indicating the result of the update operation.
     */
    public function updateSpeech(int $id, string $content): \Illuminate\Http\JsonResponse
    {
        $response = ['status' => 'error', 'message' => __('Something went wrong.')];

        try {
            \DB::beginTransaction();

            $speech = ArchiveService::show($id, 'speech_to_text_chat_reply');

            if ($speech) {
                $speech->content = $content;
                $speech->save();
                $response = ['status' => 'success', 'message' => __('Speech Updated successfully.')];
            }

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            $response['message'] = __('Failed to update speech: ') . $e->getMessage();
        }

        return response()->json($response);
    }


     /**
     * Retrieve a speech-to-text chat reply by its ID.
     *
     * @param int $id The ID of the speech record.
     * @return mixed The speech-to-text chat reply.
     */
    public static function speechById(int $id): mixed
    {
        return ArchiveService::show($id, 'speech_to_text_chat_reply');
    }

    /**
     * Deletes a speech record and associated file.
     *
     * @param int $id  The ID of the speech record to delete.
     * @return \Illuminate\Http\JsonResponse  The JSON response indicating the result of the deletion operation.
     */
    public function delete(int $id): \Illuminate\Http\JsonResponse
    {
        $response = ['status' => 'error', 'message' => __('Something went wrong.')];

        \DB::beginTransaction();
        try {
            $speech = ArchiveService::delete($id, 'speech_to_text_chat_reply');
            
            if ($speech) {
                $this->unlinkFile($speech->file_name);
                \DB::commit();

                $response = [
                    'status' => 'success',
                    'message' => __('The :x has been successfully deleted.', ['x' => __('Speech')])
                ];
            } else {
                \DB::rollBack();
            }

        } catch (\Exception $e) {
            \DB::rollBack();
            $response = ['status' => 'fail', 'message' => $e->getMessage()];
        }

        return response()->json($response);
    }


    /**
     * Unlink Audio
     * @param mixed $name
     * 
     * @return bool
     */
    protected function unlinkFile($name): bool
    {
        if (isExistFile($this->audioPath($name))) {
            objectStorage()->delete($this->audioPath($name));
        }
        return true;
    }

    /**
     * Audio's path
     * @param mixed $name
     * 
     * @return [type]
     */
    public static function audioPath($name)
    {
        return 'public' . DIRECTORY_SEPARATOR . 'uploads'. DIRECTORY_SEPARATOR . 'aiAudios'. DIRECTORY_SEPARATOR . $name;
    }

    /**
     * Get Active Users Data
     *
     * @return \Illuminate\Support\Collection
     */
    public static function users(): \Illuminate\Support\Collection
    {
        return User::where('status', 'Active')->get();
    }

    /**
     * Language Data
     *
     * @return \Illuminate\Support\Collection
     */
    public static function languages(): \Illuminate\Support\Collection
    {
        return Language::where('status', 'Active')->get();
    }

    /**
     * Updates the content of a speech record.
     *
     * @param array $data  An associative array containing the speech ID and new content.
     * @return bool  Returns `true` if the update was successful, otherwise `false`.
     */
    public function speechUpdate(array $data): bool
    {
        $speech = ArchiveService::show($data['id'], 'speech_to_text_chat_reply');
        
        if ($speech) {
            $speech->content = str_ireplace('<br>', "\n", $data['content']);
            return $speech->save();
        }

        return false;
    }

 

}
