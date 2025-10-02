<?php

/**
 * @package ImageService
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 22-03-2023
 */

 namespace Modules\OpenAI\Services;


use Illuminate\Support\Str;
use Modules\OpenAI\Entities\{
    Code,
    ContentType,
};

use App\Models\{
    User,
    Team,
    TeamMemberMeta
};

 class CodeService
 {
    /**
     * Form Data
     *
     * @var array|object
     */
    protected $formData;

    /**
     * Promt
     *
     * @var string
     */
    protected $promt;

    /**
     * Initialize
     *
     * @param object $formData
     * @return void
     */
    public function __construct($formData = null, $promt = null)
    {
        $this->formData = $formData;
        $this->promt = $promt;
    }

    /**
     * URL of API
     *
     * @return string
     */
    public function getUrl()
    {
        return config('openAI.chatUrl');
    }

    /**
     * URL of API
     *
     * @return string
     */
    public function getModel()
    {
        return config('openAI.chatModel');
    }

    /**
     * Token of chat module
     *
     * @return string
     */
    public function getToken()
    {
        return preference('max_token_length');
    }

    /**
     * get Api Key
     *
     * @return string
     */
    public function aiKey(): string
    {
        return apiKey('openai');
    }

    /**
     * Client
     *
     * @return \OpenAI\Client
     */
    public function client()
    {
        return \OpenAI::client($this->aiKey());
    }

    /**
     * Image create
     *
     * @param mixed $data
     * @return string
     */
    public function createCode($data)
    {
        $this->formData = $data;
        $this->formData['promt'] = filteringBadWords($this->formData['promt']);
        return $this->validate();
    }

    /**
     * prepare promt
     *
     * @return string
     */
    public function preparePromt()
    {
        $this->promt = ([
            'model' => $this->getModel(),
            'messages' => [
                [
                    "role" => "system",
                    "content" => "You are a great helpful assistant that writes code."
                ],
                [
                    "role" => "user",
                    "content" => "Generate code about". $this->formData['promt'] . "In" . $this->formData['language'] . "and the code level is" . $this->formData['codeLabel'],
                ],
            ],
            'temperature' => 1,
            'max_tokens' => (int) $this->getToken(),
        ]);

        return $this->getResponse();
    }

    /**
     * Get Response
     *
     * @return array
     */
    private function getResponse()
    {
        return $this->client()->chat()->create($this->promt)->toArray();
    }

    /**
     * validate form data
     *
     * @return string
     */
    public function validate()
    {
        app('Modules\OpenAI\Http\Requests\CodeStoreRequest')->safe();
        return $this->preparePromt();
    }

    /**
     * Curl Request
     *
     * @return mixed
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
        CURLOPT_POSTFIELDS => json_encode($this->promt),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . $this->aiKey(),
        ),
        ));

        // Make API request
        $response = curl_exec($curl);
        $err = curl_error($curl);
        // Close cURL session
        curl_close($curl);
        $response = !empty($response) ? $response : $err;
        return json_decode($response, true);
    }


    /**
     * Store Images
     * @param mixed $data
     *
     * @return [type]
     */
    public function save($data)
    {
        $this->formData = $data;
            $code[] = [
                'user_id' => auth('api')->user()->id,
                'model' => $data['model'],
                'slug' => $this->createSlug(request('promt')),
                'promt' => request('promt'),
                'code' => $data['choices'][0]['message']['content'],
                'tokens' => $data['usage']['total_tokens'],
                'words' =>  preference('word_count_method') == 'token' ? subscription('tokenToWord', $data['usage']['total_tokens']) : countWords($data['choices'][0]['message']['content']),
                'characters' => strlen($data['choices'][0]['message']['content']),
                'language' => request('language'),
                'code_label' => request('codeLabel'),

            ];
            

       return $this->storeData($code);
    }

    /**
     * Image store in DB
     * @param mixed $images
     *
     * @return bool|array
     */
    protected function storeData($code)
    {
       return Code::insert($code) ? $this->formData : false;
    }

    /**
     * Team member meta insert or update
     * @param mixed $code
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
     * Image Name Creation
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

            if (Code::whereSlug($slug)->exists()) {
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
    public static function model()
    {
        return Code::with(['user:id,name']);
    }

    /**
     * Get All codes
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAll()
    {
        $result = self::model()->with(['user']);
        $result = $result->where('user_id', auth()->user()->id);
        
        return $result->latest();
    }

    /**
     * Find code by slug
     * @param mixed $slug
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function codeBySlug($slug)
    {
        return self::model()->whereSlug($slug)->firstOrFail();
    }

    /**
     * Find code by slug
     *
    * @param string $slug
    * @return \Illuminate\Database\Eloquent\Model
    */
    public function codeById($slug)
    {
        return self::model()->whereId($slug)->firstOrFail();
    }

    /**
     * delete code using id
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $data = $this->codeById($id);
        return !empty($data) ? $this->codeById($id)->delete() : false;
    }

     /**
     * get meta data
     *
     * @param array $data
     * @return array
     */
    public function getMeta($slug = null)
    {
        return ContentType::getData($slug);
    }

     /**
     * Users Data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function users()
    {
        return User::get();
    }

 }
