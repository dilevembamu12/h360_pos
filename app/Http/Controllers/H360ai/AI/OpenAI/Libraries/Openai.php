<?php

/**
 * @package OpenAI
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 24-05-2023
 */

namespace Modules\OpenAI\Libraries;

 class Openai
 {
    protected $url = 'https://api.openai.com/v1/images/generations';
    protected $promt;
    protected $imageService;
    protected $model = 'dall-e-2';

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct($imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * prepare promt
     * @return [type]
     */
    public function promt($data)
    {
        $style = isset($data['lightingStyle']) ? ' in a ' . $data['lightingStyle'] : '';
        $style .= isset($data['artStyle']) ? ' in a ' . $data['artStyle'] : '';
        $this->promt = [
            "prompt" => 'Please generate images of' . $data['promt']. 'in a ' . $style,
            "n" => isset($data['variant']) ? (int) $data['variant'] : 1,
            "size" => isset($data['resulation']) ? $data['resulation'] : '1024x1024',
            "model" => request('model') ?? preference('openai_engine')
        ];

        return $this->response($this->getResponse());
    }

    /**
     * Get Response
     *
     * @return array
     */
    public function getResponse() {
        $client = \OpenAI::client(apiKey('openai'));

        return $client->images()->create($this->promt);
    }

    /**
     * Curl Request
     * @return [type]
     */
    public function response($response)
    {
        if (isset($response['created'])) {
            return $this->save($response);
        } else if(isset($response['error'])) {
            return [
                'response' => $response['error']['message'],
                'status' => 'error',
            ];
        }

    }

    /**
     * Store Images
     * @param mixed $data
     *
     * @return [type]
     */
    public function save($data)
    {
        $totalImages = count($data['data']);
        $responseArray = $data->toArray(); // Convert the response object to an array
        $responseArray['created-at'] = time();
        $id = null;

        for ($i = 0; $i < $totalImages; $i++) {
            $this->imageService->upload($data['data'][$i]['url']);
            $slug = $totalImages > 1 ? $this->imageService->createSlug(request('promt') . $i) : $this->imageService->createSlug(request('promt'));
            $name = $this->imageService->createName(request('promt'));

            // Optionally check and convert encoding if necessary
            if (!mb_check_encoding($slug, 'UTF-8')) {
                $slug = mb_convert_encoding($slug, 'UTF-8');
            }
            if (!mb_check_encoding($name, 'UTF-8')) {
                $name = mb_convert_encoding($name, 'UTF-8');
            }
            
            $images = [
                'user_id' => auth('api')->user()->id,
                'parent_id' => request('parent_id') ?? $id,
                'name' => $name,
                'original_name' => $this->imageService->imageName,
                'promt' => request('promt'),
                'slug' => $slug,
                'size' => request('resulation'),
                'art_style' => request('artStyle'),
                'lighting_style' => request('lightingStyle'),
                'libraries' => 'Openai',
                'meta' => json_encode($responseArray),
            ];

            $id = $this->imageService->storeData($images);
            $urlWithParams = url('user/image-gallery?slug=' . $slug);
            $imageNames[] = [
                'id' => $id,
                'url' => $this->imageService->storagePath() . DIRECTORY_SEPARATOR . $this->imageService->imageName,
                'slug_url' => $urlWithParams,
                'name' => $name,
                'size' => request('resulation'),
                'artStyle' => request('artStyle'),
                'lightingStyle' => request('lightingStyle'),
                'created_at' => now(),
                'libraries' => 'Openai'
            ];
        }

       return $imageNames;
    }

 }
