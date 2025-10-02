<?php

/**
 * @package ContentService
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 07-03-2023
 */

namespace Modules\OpenAI\Services;

use App\Models\{
    Language,
    User,
    Team,
    TeamMemberMeta
};
use App\Traits\ApiResponse;
use Modules\OpenAI\Entities\OpenAI;
use Modules\OpenAI\Entities\{
    Content,
    ContentTypeMeta,
    ContentType,
    UseCase,
    Option,
    UseCaseCategory,
    Archive
};
use Modules\OpenAI\Traits\MetaTrait;

 class ContentService
 {
    use MetaTrait, ApiResponse;
    protected $formData;
    protected $preparedData;
    protected $response;
    protected $failedResponse;

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct($formData = null, $preparedData = null, $response = null, $failedResponse = null)
    {
        $this->formData = $formData;
        $this->preparedData = $preparedData;
        $this->response = $response;
        $this->failedResponse = [
            'status' => 'failed',
        ];
    }

    /**
     * Prapare data for insertation
     *
     * @param mixed $data
     * @param mixed $useCaseId
     * @param mixed $promt
     * @param mixed $response
     *
     * @return [type]
     */
    public function prepareData($data, $useCaseId, $promt, $response, $streamedText = 0)
    {
        $contents = '';
        $promtText = array_values(json_decode($data['questions'], true));
        $model = preference('ai_model');
        if (!OpenAI::requiredStremedData()) {
        $responses = count($response['choices']);
            for ($i = 0; $i < $responses; $i++) {
                    $contents .= in_array($model, $this->chatModel()) ? $response['choices'][$i]['message']['content'] : $response['choices'][$i]['text'];
                    $totalWords = preference('word_count_method') == 'token' ? subscription('tokenToWord', $response['usage']['total_tokens']) : countWords($contents);
                }
        } else {
            $wordCount = count(explode(' ', $streamedText));
            $totalWords = preference('word_count_method') == 'token' ? subscription('tokenToWord', $wordCount) : countWords($streamedText);
        }

        $preparedData =  [
            'user_id' => auth('api')->user()->id,
            'use_case_id' => $useCaseId,
            'title' => implode(',', $promtText),
            'slug' => $this->createSlug(implode(',', $promtText)),
            'promt' => $promt,
            'content' => $streamedText ?? $contents,
            'tokens' => $totalWords,
            'words' => $totalWords,
            'characters' => strlen($streamedText),
            'model' => preference('ai_model'),
            'language' => $data['language'],
            'tone' => $data['tone'],
            'creativity_label' => $data['temperature'],
        ];
        $this->response = $response;
        // Added total used word in array
        $this->response->words = $totalWords;
        $this->preparedData = $preparedData;
        if (request('contentSlug') && !empty(request('previousContent'))) {
            return $this->update();
         }
        // Prepare data for validation
        request()->merge($preparedData);
        return $this->validate();
    }

    /**
     * validate form data
     * @return [type]
     */
    public function validate()
    {
        app('Modules\OpenAI\Http\Requests\ContentStoreRequest')->safe();
        return $this->store();
    }

     /**
     * Store Content
     *
     * @return [type]
     */
    public function store()
    {
       return Content::insert($this->preparedData) ? $this->response : $this->failedResponse;
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
     * Update Content
     *
     * @return [type]
     */
    protected function update()
    {
        $response = ['status' => 'fail', 'message' => __('The :x does not exist.', ['x' => __('Content')])];
        $content = Content::where('slug', request('contentSlug'))->first();

        if ($content) {
            $content->content = $content->content . '<br /><br />' . nl2br($this->response[0]['text']);
            $content->words = $content->words + countWords($this->response[0]['text']);
            $content->characters = $content->characters + strlen($this->response[0]['text']);
            $content->save();

            return response()->json(['status' => 'success', 'message' => __('The :x has been successfully saved.', ['x' => __('Content')])]);
        }

        return response()->json($response);
    }

     /**
     * Create slug
     *
     * @param mixed $name
     * @return string
     */
    protected function createSlug($name)
    {
        if (!empty($name)) {

            $slug = cleanedUrl($name);

            if (Content::whereSlug($slug)->exists()) {
                $slug = $slug . '-' . time();
            }

            return $slug;
        }
    }

    /**
     * Get All Data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        $result = $this->model()->with('useCase');
        $result = $result->where('user_id', auth()->user()->id);

        return $result->whereNull('parent_id')->latest();
    }

    /**
     * Get All Favorite
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFavourite()
    {
        $bookmarks = auth()->user()->document_bookmarks_openai;

        $result = $this->model()->with(['useCase'])->whereIn('id', $bookmarks);
        
        return $result->where('type', 'template_chat')->latest();
    }

    /**
     * Content By Slug
     *
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function contentBySlug($slug)
    {
        return Content::with(['useCase', 'User'])->whereSlug($slug)->firstOrFail();
    }

    /**
     * Use Cases
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function useCases($data = null)
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
    public static function languages()
    {
        return Language::where(['status' => 'Active'])->get();
    }

    /**
     * Users Data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function users()
    {
        return User::where(['status' => 'Active'])->get();
    }

    /**
     * Content Model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function model()
    {
        return Archive::with(['metas']);
    }

    /**
     * Get Content
     *
     * @param mixed $contentId
     * @return array
     */
    public function getContent($contentId)
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
    protected function getQuestions($string)
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
    public static function getOption($useCaseId)
    {
        return Option::whereUseCaseId($useCaseId)->get();
    }

    /**
     * @param mixed $id
     *
     * @return [type]
     */
    public static function delete($id)
    {
        if ($content = Content::find($id)) {
            try {
                $content->delete();
                $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Content')])];

            } catch (\Exception $e) {
                $response = ['status' => 'fail', 'message' => $e->getMessage()];
            }
            return response()->json($response);

        }
        $response = ['status' => 'fail', 'message' => __('The data you are looking for is not found')];
        return response()->json($response);
    }

    /**
     * Update Content
     *
     * @param string $contentSlug
     * @param string $contents
     * @return \Illuminate\Http\Response
     */
    public function updateContent($contentSlug, $contents)
    {
        $response = ['status' => 'error', 'message' => __('The :x does not exist.', ['x' => __('Content')])];
        $content = Content::where('slug', $contentSlug)->first();

        if ($content) {
            $content->content = $contents;
            $content->save();
            $response = ['status' => 'success', 'message' => __('The Content Updated successfully.')];
        }

        return response()->json($response);
    }

    /**
     * Create Version
     *
     * @param object $content
     * @param array $data
     * @return bool
     */
    public function createVersion($content, $data)
    {
        $content->content = str_ireplace('<br>', "\n", $data['content']);
        return $content->save();
    }

    /**
     * Content Update
     *
     * @param array $data
     * @return bool
     */
    public function contentUpdate($data)
    {
        $content = Content::where('id', $data['id'])->firstOrFail();
        $content->content = str_ireplace('<br>', "\n", $data['content']);
        $content->use_case_id = $data['use_case_id'];
        return $content->save();
    }

    /**
     * Use Case Categories
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function useCaseCategories()
    {
       return UseCaseCategory::with([
            'useCases' => function ($query) {
                $query->select('id', 'name', 'description', 'slug')
                    ->where('status', 'Active');
            }
        ])->get();
    }

    /**
     * Contents Features
     *
     * @return array
     */
    public static function features(): array
    {
        /**
         * Type will be bool, number, string
         * title_position will be before, after
         * When added new key and value it will need to add in blade file
         */
        return [

            'document' => [

                'tone' => [
                    'Casual' => 'Casual',
                    'Funny' => 'Funny',
                    'Bold' => 'Bold',
                    'Feminine' => 'Feminine',
                    'Professional' => 'Professional',
                    'Friendly' => 'Friendly',
                    'Dramatic' => 'Dramatic',
                    'Playful' => 'Playful',
                    'Excited' => 'Excited',
                    'Sarcastic' => 'Sarcastic',
                    'Empathetic' => 'Empathetic'
                ],

                'variant' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                ],

                'temperature' => [
                    'Optimal' => 'Optimal',
                    'Low' => 'Low',
                    'Medium' => 'Medium',
                    'High' => 'High',
                ]

            ],

            'image_maker' => [

                'openai_variant' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                ],
                
                'stable_diffusion_variant' => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                ],

                'clipdrop_variant' => [
                    '1' => '1',
                ],

                'openai_resulation' => [
                    '256x256' => '256x256',
                    '512x512' => '512x512',
                    '1024x1024' => '1024x1024',
                    '1024x1792' => '1024x1792',
                    '1792x1024' => '1792x1024',
                ],

                'stable_diffusion_resulation' => [
                    '512x512' => '512x512',
                    '512x768' => '512x768',
                    '512x896' => '512x896',
                    '512x1024' => '512x1024',
                    '640x1536' => '640x1536',
                    '768x512' => '768x512',
                    '768x768' => '768x768',
                    '768x1024' => '768x1024',
                    '768x1152' => '768x1152',
                    '768x1344' => '768x1344',
                    '832x1216' => '832x1216',
                    '896x512' => '896x512',
                    '896x1152' => '896x1152',
                    '1024x512' => '1024x512',
                    '1024x768' => '1024x768',
                    '1024x1024' => '1024x1024',
                    '1152x768' => '1152x768',
                    '1152x896' => '1152x896',
                    '1344x768' => '1344x768',
                    '1536x640' => '1536x640',
                ],

                'clipdrop_resulation' => [
                    '1024x1024' =>  '1024x1024',
                ],

                'openai_artStyle' => [
                    'Normal' => 'Normal',
                    'Cartoon art' => 'Cartoon art',
                    '3D Render' => '3D Render',
                    'Pixel art' => 'Pixel art',
                    'Isometric' => 'Isometric',
                    'Vendor art' => 'Vendor art',
                    'Line art' => 'Line art',
                    'Watercolor art' => 'Watercolor art',
                    'Anime art' => 'Anime art'
                ],

                'stable_diffusion_artStyle' => [
                    'Normal' => 'Normal',
                    'Cartoon art' => 'Cartoon art',
                    '3D Render' => '3D Render',
                    'Pixel art' => 'Pixel art',
                    'Isometric' => 'Isometric',
                    'Vendor art' => 'Vendor art',
                    'Line art' => 'Line art',
                    'Watercolor art' => 'Watercolor art',
                    'Anime art' => 'Anime art'
                ],

                'clipdrop_artStyle' => [
                    'Normal' => 'Normal',
                    'Cartoon art' => 'Cartoon art',
                    '3D Render' => '3D Render',
                    'Pixel art' => 'Pixel art',
                    'Isometric' => 'Isometric',
                    'Vendor art' => 'Vendor art',
                    'Line art' => 'Line art',
                    'Watercolor art' => 'Watercolor art',
                    'Anime art' => 'Anime art'
                ],

                'openai_lightingStyle' => [
                    'Normal' => 'Normal',
                    'Studio' => 'Studio',
                    'Warm' => 'Warm',
                    'Cold' => 'Cold',
                    'Ambient' => 'Ambient',
                    'Neon' => 'Neon',
                    'Foggy' => 'Foggy'
                ],

                'stable_diffusion_lightingStyle' => [
                    'Normal' => 'Normal',
                    'Studio' => 'Studio',
                    'Warm' => 'Warm',
                    'Cold' => 'Cold',
                    'Ambient' => 'Ambient',
                    'Neon' => 'Neon',
                    'Foggy' => 'Foggy'
                ],

                'clipdrop_lightingStyle' => [
                    'Normal' => 'Normal',
                    'Studio' => 'Studio',
                    'Warm' => 'Warm',
                    'Cold' => 'Cold',
                    'Ambient' => 'Ambient',
                    'Neon' => 'Neon',
                    'Foggy' => 'Foggy'
                ],

                'imageCreateFrom' => [
                    'openai' => 'OpenAI',
                    'stable_diffusion' => 'Stable Diffusion',
                    'clipdrop' => 'Clipdrop',
                ]

            ],

            'code_writer' => [

                'language' => [
                    'PHP' => 'PHP',
                    'Java' =>  'Java',
                    'Rubby' =>  'Rubby',
                    'Python' => 'Python',
                    'C#' =>'C#' ,
                    'Go' => 'Go',
                    'Kotlin' => 'Kotlin',
                    'HTML' => 'HTML',
                    'Javascript' => 'Javascript',
                    'TypeScript' => 'TypeScript',
                    'SQL' => 'SQL',
                    'NoSQL' => 'NoSQL'
                ],

                'codeLabel' => [
                    'Noob' => 'Noob',
                    'Moderate' => 'Moderate',
                    'High' => 'High',
                ]
            ],

            'text_to_speech' => [

                'volume' => [
                    'Low' => 'Low',
                    'Default' =>  'Default',
                    'High' =>  'High',
                ],

                'gender' => [
                    'Male' => 'Male',
                    'Female' => 'Female',
                ],

                'pitch' => [
                    'Low' => 'Low',
                    'Default' =>  'Default',
                    'High' =>  'High',
                ],

                'speed' => [
                    'Super Slow' => 'Super Slow',
                    'Slow' =>  'Slow',
                    'Default' =>  'Default',
                    'Fast' =>  'Fast',
                    'Super Fast' =>  'Super Fast',
                ],

                'pause' => [
                    '0s' => '0s',
                    '1s' =>  '1s',
                    '2s' =>  '2s',
                    '3s' =>  '3s',
                    '4s' =>  '4s',
                    '5s' =>  '5s',
                ],

                'audio_effect' => [
                    'Smart Watch' => 'Smart Watch',
                    'Smartphone' =>  'Smartphone',
                    'Headphone' =>  'Headphone',
                    'Bluetooth' =>  'Bluetooth',
                    'Smart Bluetooth' =>  'Smart Bluetooth',
                    'Smart TV' =>  'Smart TV',
                    'Car Speaker' =>  'Car Speaker',
                    'Telephone' =>  'Telephone',
                ],

                'target_format' => [
                    'MP3' => 'MP3',
                    'WAV' =>  'WAV',
                    'OGG' =>  'OGG',
                ],
                
            ],

        ];
    }

    /**
     * Store meta data
     *
     * @param array $data
     * @return array
     */
    public function storeMeta($metaData)
    {
        $properties = [];

        if (is_array($metaData) || is_object($metaData)) {
            foreach ($metaData as $key => $data) {
                $id = $this->contentType($key);

                foreach ($data as $k => $value) {
                    $properties[] = [
                        'content_type_id' => $id,
                        'name' => $key,
                        'key' => $k,
                        'value' => json_encode($value),
                    ];
                }
            }
            ContentTypeMeta::upsert($properties, ['content_type_id','key']);
            $response = ['status' => 'success', 'message' => __('The :x has been successfully saved.', ['x' => __('AI Preference Settings')])];
        } else {
            $response = ['status' => 'fail', 'message' => __('At Least one Item has to be selected.')];
        }

        return $response;
    }


    /**
     * get all preferences meta data
     *
     * @param array $data
     * @return array
     */
    public function getAllMeta()
    {
        return ContentType::get();
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
     * Process input value
     * @param mixed $value
     * @return mixed
     */
    private function contentType($value)
    {
        return ContentType::where('slug',$value)->value('id');
    }

     /**
     * get use cases by slug
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function useCasesbySlug($name)
    {
        $parent = self::contentBySlug($name);
        $child = Content::with(['useCase'])->where('id', $parent->id)->orWhere('parent_id', $parent->id)->get();
        return !empty($child) ? $child : $parent;
    }

    /**
     * define chat model
     * @return array
     */
    public function chatModel()
    {
        return [
            'gpt-4o',
            'gpt-4o-mini',
            'gpt-3.5-turbo',
            'gpt-4',
            'gpt-3.5-turbo-16k',
            'gpt-4-1106-preview',
            'gpt-3.5-turbo-1106',
            'gpt-4-0125-preview',
            'gpt-3.5-turbo-0125',
        ];
    }

    /**
     * Get current member from session or meta
     * 
     * @param string $currentPackageMeta
     * @param string $currentPackageSession
     * @return [type]
     */
    
    public function getCurrentMemberUserId($currentPackageMeta = null, $currentPackageSession = null, $data = [])
    {
        $userId = isset($data['user_id']) ? $data['user_id'] : auth()->user()->id;
        $memberData = Team::getMember($userId);

        // If member data exists
        if (!empty($memberData)) {
            // If user doesn't have credit details
            if (!subscription('hasCreditDetails', $userId)) {
                return $memberData->parent_id;
            }

            // Handle package meta
            if ($currentPackageMeta === 'meta') {
                $memberPackageData = Team::memberSession();

                if ($memberPackageData) {
                    return $memberPackageData->value;
                }

                return $this->unprocessableResponse([
                    'response' => __('Please set your plan from settings'),
                    'status' => 'failed',
                ]);
            }

            // Handle package session
            if ($currentPackageSession === 'session') {
                return session('memberPackageData.packageUser', 0);
            }

            return 0;
        }

        return $userId;
    }

    /**
     * Check parent or member user status
     * 
     * @param string $userId
     * @param string $type
     * @return [type]
     */
    public function checkUserStatus($userId, $type)
    {
        if ($type == 'meta') {
            $userStatus = User::where(['id' => $userId, 'status' => 'Active'])->first();
            if (!empty($userStatus) || $userId == 0) {
                return [
                    'message' => __('Subscribed user is active'),
                    'status' => 'success',
                ];
            } else {
                return [
                    'message' => __('The account of the user whose credits you\'re using is not active. Kindly reach out to administration for help.'),
                    'status' => 'fail',
                ];
            }
        }
    }

    public function getCurrentMemberUser()
    {
        $authUserId = auth()->user()->id;
        $memberData = Team::getMember($authUserId);
        if (!empty($memberData)) {
            if (subscription('isSubscribed', $authUserId)) {
                
            } else {
                $userId = $memberData->parent_id;
            }
        } else {
            $userId = $authUserId;
        }
        return $userId;
    }

    /**
     * @param mixed $model
     * @param mixed $contentService
     * @param mixed $templateService
     * @param mixed $subscription
     * @param mixed $useCase
     * @param mixed $userId
     * 
     * @return [type]
     */
    public function streamResponse($model, $contentService, $templateService, $subscription, $useCase, $userId)
    {
        return response()->stream(function () use ($model, $contentService, $templateService, $subscription, $useCase, $userId) {
            try {
                $prompt = '';
                if ( (int) request('variant') > 1 ) {
                    $prompt .='. Create seperate distinct ' . request('variant') . ' results.';
                }
                if (in_array($model, $contentService->chatModel())) {
                    $result = OpenAI::getClient()->chat()->createStreamed([     
                        'model' => $model,
                        'messages' => [
                            [
                                "role" => "user",
                                "content" =>  'Provide a response in' . request('language'). 'language/n '. $templateService->render() . $prompt .' '. 'and please keep the tone ' .' '. request('tone')
                            ],
                        ],
                        'temperature' => (float) request('temperature'),
                    ]); 
                } else {
                        $result = OpenAI::completions([
                            'prompt' =>  'Provide a response in' . request('language'). 'language/n '. $templateService->render() . $prompt .' '. 'and please keep the tone ' .' '. request('tone'),
                            'temperature' => (float) request('temperature'),
                        ]);
                    }
                    $textValue = '';
                ob_start();
                foreach ($result as $response) {
                    if (in_array($model, $contentService->chatModel())) {
                        $text = $response->choices[0]->delta->content;
                        $textValue .= $text;
                        if (connection_aborted()) {
                            break;
                        }
                        echo ($text);
                        ob_flush();
                        flush();

                    } else {
                        $text = $response->choices[0]->text;
                        $textValue .= $text;
                        if (connection_aborted()) {
                            break;
                        }
                        echo $text;
                        ob_flush();
                        flush();
                    }
                  
                      
                }

                if ($result) {
                    $response = $contentService->prepareData(request()->all(), $useCase->id, $templateService->render(), $result, $textValue);
                    $response->words = subscription('tokenToWord', countWords($textValue));
                    $response->text = $textValue;
                    $response->balanceReduce = 'onetime';
                    if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
                        $increment = subscription('usageIncrement', $subscription?->id, 'word', $response->words, $userId);
                        if ($increment  && $userId != auth()->user()->id) {
                            $contentService->storeTeamMeta($response->words);
                        }
                        $response->balanceReduce = app('user_balance_reduce');
                    }
                    return $this->successResponse($response);
                }
            } catch (\Exception $e) {
                ob_start();
                $response = $e->getMessage();
                $data = [
                    'text' => $response,
                    'status' => false,
                    'code' => 500,
                ];
                echo json_encode($data);
                ob_flush();
                flush();
            }
            
        }, 200, [
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Content-Type' => 'json',
        ]); 
    }

    /**
     * @param mixed $model
     * @param mixed $contentService
     * @param mixed $templateService
     * @param mixed $subscription
     * @param mixed $useCase
     * @param mixed $userId
     * 
     * @return [type]
     */
    public function generalResponse($model, $contentService, $templateService, $subscription, $useCase, $userId)
    {
        try {
            if (in_array($model, $contentService->chatModel())) {
                $result = OpenAI::contentCreate([
                    'model' => $model,
                    'messages' => [
                        [
                            "role" => "user",
                            "content" =>  'Provide a response in' . request('language'). 'language/n '. $templateService->render() . ' ' . 'and please keep the tone ' .' '. request('tone')
                        ],
                    ],
                    'temperature' => (float) request('temperature'),
                    'n' => (int) request('variant')
                ]); 
            } else {
                $result = OpenAI::completions([
                    'prompt' => 'Provide a response in' . request('language'). 'language/n '. $templateService->render() . ' ' . 'and please keep the tone ' .' '. request('tone'),
                    'temperature' => (float) request('temperature'),
                    'n' => (int) request('variant')
                ]);
                }
            if ($result) {
                $response = $contentService->prepareData(request()->all(), $useCase->id, $templateService->render(), $result, null);
                $response->words = subscription('tokenToWord', $response->usage->totalTokens);

                $response->balanceReduce = 'onetime';
                if (!subscription('isAdminSubscribed') || auth()->user()->hasCredit('word')) {
                    $increment = subscription('usageIncrement', $subscription?->id, 'word', $response->words, $userId);
                    if ($increment  && $userId != auth()->user()->id) {
                        $contentService->storeTeamMeta($response->words);
                    }
                    $response->balanceReduce = app('user_balance_reduce');
                }
                return $this->successResponse($response);
            }

        } catch (\Exception $e) {
            $response = $e->getMessage();
            $data = [
                'response' => $response,
                'status' => 'failed',
            ];

            return $this->unprocessableResponse($data);
        }
    }

    /**
     * List the provideres with his model
     * @return [type]
     */
    public function aiProviders()
    {
        $providers = config('openAI.providers');
        $allProviders = [];
        foreach($providers as $key => $provider) {
            $allProviders[$key] = [];
        }

        $allProviders['stableDiffusion'] += config('openAI.stableDiffusion');
        $allProviders['openAI'] += config('openAI.openAIModel');
        return $allProviders;
    }


    /**
     * @param array $data
     * 
     * @return array
     */
    public function processImageData($data)
    {
        $processData = [];

        foreach ($data['metadata'] as $meta) {
            if ($meta->key == 'imageCreateFrom') {
                $prefixes = json_decode($meta->value, true);
                $processData[$meta->key] = processApiPreferenceData($meta->key, json_decode($meta->value, true));
                break;
            }
        };

        foreach ($prefixes as $prefix) {
            foreach ($data['metadata'] as $innerMeta) {
                if (strpos($innerMeta->key, $prefix) !== false) {
                    $processData[$prefix][] = [
                        'key' => $innerMeta->key,
                        'value' => processApiPreferenceData($innerMeta->key, json_decode($innerMeta->value, true))
                    ];
                }
            }
        }
        
        return $processData;
    }

 }
