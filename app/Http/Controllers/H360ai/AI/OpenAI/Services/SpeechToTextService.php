<?php

/**
 * @package SpeechToTextService
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <[shakib.techvill@gmail.com]>
 * @created 22-03-2023
 */

namespace Modules\OpenAI\Services;

use Modules\OpenAI\Entities\Speech;

use App\Models\{
    User,
    Language,
    Team,
    TeamMemberMeta
};

 class SpeechToTextService
 {
    /**
     * @var [type]
     */
    protected $formData;
    protected $prompt;
    protected $originalFileName;
    protected $fileSize;
    protected $wordFilter;
    protected $duration;

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */

    public function __construct($formData = null, $prompt = null, $originalFileName = null, $fileSize = null, $wordFilter = null, $duration = null)
    {
        $this->formData = $formData;
        $this->prompt = $prompt;
        $this->originalFileName = $originalFileName;
        $this->fileSize = $fileSize;
        $this->wordFilter = $wordFilter;
        $this->duration = $duration;
    }

    /**
     * URL of API
     * @return [type]
     */

    public function getUrl()
    {
        return config('openAI.speechUrl');
    }

    /**
     * URL of API
     * @return [type]
     */

    public function getModel()
    {
        return config('openAI.speechModel');
    }

    /**
     * Token of chat module
     * @return [type]
     */

    public function getToken()
    {
        return config('openAI.chatToken');
    }

    /**
     * get Api Key
     * @return [type]
     */

    public function aiKey(): string
    {
        return apiKey('openai');
    }

    /**
     * Generate text
     * @param mixed $data
     *
     * @return [type]
     */

    public function generateText($data)
    {
        $this->formData = $data;
        $this->wordFilter = $data['word_filter'];
        $this->duration = $data['duration'];
        return $this->validate();
    }

    /**
     * prepare prompt
     * @return [type]
     */

    public function preparePrompt()
    {   
        $this->prompt = ([
            'model' => $this->getModel(),
            'language' => $this->formData['language'],
            'file' => $this->prepareFile(),
            'response_format'=> "json",
        ]);
        return $this->makeCurlRequest();
    }

    /**
     * prepare file
     * @return [type]
     */

     public function prepareFile()
     {   
 
        $uploadedFile = $this->formData['file'];

        $this->fileSize = $this->getFileSize($uploadedFile);

        $originalFileName = $uploadedFile->getClientOriginalName();
        $this->originalFileName = $originalFileName;

        $file = new \CURLFile($uploadedFile->getRealPath(), $uploadedFile->getMimeType(), $originalFileName);
        return $file;
     }

    /**
     * prepare file size
     * 
     * @param $file array
     * 
     * @return [type]
     */
    protected function getFileSize($file = [])
    {
        $bytes = filesize($file);
        return round($bytes / 1024, 2);
    }

     /**
     * validate form data
     * @return [type]
     */

     public function validate()
     {
         app('Modules\OpenAI\Http\Requests\SpeechStoreRequest')->safe();
         return $this->preparePrompt();
     }

     /**
     * Create upload path
     * @return [type]
     */
    protected function uploadPath()
	{
		return createDirectory(join(DIRECTORY_SEPARATOR, ['public', 'uploads','aiAudios']));
	}

    /**
     * Curl Request
     * @return [type]
     */
    public function makeCurlRequest()
    {
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getUrl(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => config('openAI.ssl_verify_host'),
            CURLOPT_SSL_VERIFYPEER => config('openAI.ssl_verify_peer'),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->prompt,
            CURLOPT_HTTPHEADER => [
                'content-type: multipart/form-data',
                "Authorization: Bearer " . $this->aiKey(),
            ],
        ));
        
        // Make API request
        $response = curl_exec($curl);
        $err = curl_error($curl);
        // Close cURL session
        curl_close($curl);

        $response = !empty($response) ? $response : $err;

        $responseData = json_decode($response, true);

        return $responseData;
    }


    /**
     * Store Speech
     * @param mixed $data
     *
     * @return [type]
     */

    public function save($data)
    {
        $fileName = $this->storeFile();
        $this->formData = $data;
        $this->formData['text'] =  ( $this->wordFilter === 'active' ) ? filteringBadWords($this->formData['text']) : $this->formData['text'];
        $speech[] = [
            'user_id' => auth('api')->user()->id,
            'content' => ( $this->wordFilter === 'active' ) ? filteringBadWords($data['text']) : $data['text'],
            'duration' => $this->duration,
            'language' => $this->processLanguageData(request('language')),
            'file_name' => $fileName,
            'original_file_name' => $this->originalFileName,
            'file_size' => $this->fileSize,
        ];

       return $this->storeData($speech);
    }

    /**
     * Store file
     * @return [type]
     */

     public function storeFile()
     {  
        $this->uploadPath();

        $uploadedFile = $this->formData['file'];
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
     * Store speech in DB
     * @param mixed $images
     *
     * @return [type]
     */

    protected function storeData($speech)
    {
       return Speech::insert($speech) ? $this->formData : false;
    }

    /**
     * @return [type]
     */

    public function getAll() {

        $result = Speech::with(['user:id,name']);
 
        $result = $result->where('user_id', auth()->user()->id);

        return $result->latest();
    }

    /**
     * @return [type]
     */

    public static function speechById($id) {
        return Speech::with(['user:id,name'])->where('id', $id)->firstOrFail();
    }

    /**
     * Speech Update
     * @param array $data
     * @return [type]
     */

    public function updateSpeech($id, $content)
    {
        $response = ['status' => 'error', 'message' => __('Something went wrong.')];
        $speech = Speech::where('id', $id)->first();

        if ($speech) {
            $speech->content = $content;
            $speech->save();
            $response = ['status' => 'success', 'message' => __('Speech Updated successfully.')];
        }

        return response()->json($response);
    }

    /**
     * @param mixed $id
     *
     * @return [type]
     */

    public function delete($id)
    {
        if ($speech = Speech::find($id)) {
            try {
                $deleted = $speech->delete();
                
                if ($deleted) {
                    $this->unlinkFile($speech->file_name);
                    $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Speech')])];
                }
                

            } catch (\Exception $e) {
                $response = ['status' => 'fail', 'message' => $e->getMessage()];
            }
            return response()->json($response);

        }

        $response = ['status' => 'fail', 'message' => __('The data you are looking for is not found')];
        return response()->json($response);
    }

    /**
     * Language Data
     * @return [type]
     */

    public static function languages()
    {
        return Language::where('status', 'Active')->get();
    }

    /**
     * Process Language Data
     * 
     * @param string $lang
     * 
     * @return [type]
     */

    protected function processLanguageData($lang)
    {
        $languages = Language::where('status', 'Active')->pluck('name','short_name') ?? [];

        if (!count($languages) == 0) {
            return $languages[$lang];
        }
        return $lang;
    }

    /**
     * Users Data
    * @return [type]
    */
 
    public static function users()
    {
        return User::get();
    }

    /**
     * Speech Update
     * @param array $data
     * @return [type]
     */

    public function speechUpdate($data)
    {
        $speech = Speech::where('id', $data['id'])->first();
        $speech->content = str_ireplace('<br>', "\n", $data['content']);
        return $speech->save();
    }

    /**
     * Unlink Audio
     * @param mixed $name
     * 
     * @return [type]
     */
    protected function unlinkFile($name)
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
     * Core Model
     * @return [type]
     */

    public static function model()
    {
        return Speech::with(['user:id,name']);
    }

    /**

     * Team member meta insert or update
     * @param array $words
     *
     * @return bool|array
     */
    public function storeTeamMeta($minutes)
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

 }
