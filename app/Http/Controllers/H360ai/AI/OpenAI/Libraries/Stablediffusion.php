<?php

/**
 * @package Stable Diffusion
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 24-05-2023
 */

namespace Modules\OpenAI\Libraries;

 class Stablediffusion
 {
    /**
     * URL
     *
     * @var string
     */
    protected $url;
     /**
     * Image URL
     *
     * @var string
     */
    protected $Imageurl;
    /**
     * Promt
     *
     * @var string
     */
    protected $promtText;

    /**
     * Image to Image Prompt
     *
     * @var string
     */
    protected $imageToImagePromt;

    /**
     * Image service
     *
     * @var object
     */
    protected $imageService;

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct($imageService)
    {
        $model = request('model') ?? preference('stable_diffusion_engine');
        $this->imageService = $imageService;
        $this->url = "https://api.stability.ai/v1/generation/" . $model . "/text-to-image";
        $this->Imageurl = "https://api.stability.ai/v1/generation/" . $model . "/image-to-image";
    }

    /**
     * Image to Image Conversion
     *
     * @param array $data
     * @return void
     */
    public function imageToImage($data)
    {
        $style = isset($data['lightingStyle']) ? ' in a ' . $data['lightingStyle'] : '';
        $style .= isset($data['artStyle']) ? ' in a ' . $data['artStyle'] : '';

        $this->imageToImagePromt = ([
            "text_prompts[0][text]" => 'Please generate image of ' . $data['promt'] . $style,
            "text_prompts[0][weight]" => 0.7,
            "init_image_mode" => "IMAGE_STRENGTH",
            "image_strength" => 0.8,
            "cfg_scale" => 7,
            "clip_guidance_preset" => 'FAST_BLUE',
            "samples" => isset($data['variant']) ? (int) $data['variant'] : 1,
            "steps" => 30,
            "init_image" => file_get_contents(request('file')),
        ]);

        return $this->makeCurlRequest();
    }


    /**
     * Text to Image
     * @param mixed $data
     *
     * @return [type]
     */
    public function generalPromt($data)
    {
        $imgHeightWidth = $this->imageService->explodedData(request('resulation'));

        $style = isset($data['lightingStyle']) ? ' in a ' . $data['lightingStyle'] : '';
        $style .= isset($data['artStyle']) ? ' in a ' . $data['artStyle'] : '';
        $this->promtText = [
            "text_prompts" => [
                    [
                        "text" => 'Please generate image of ' . $data['promt'] . $style
                    ]
            ],
            "cfg_scale" => 7,
            "clip_guidance_preset" => 'FAST_BLUE',
            "height" => isset($imgHeightWidth[1]) ? (int) $imgHeightWidth[1] : 512,
            "width" => isset($imgHeightWidth[0]) ? (int) $imgHeightWidth[0] : 512,
            "samples" => isset($data['variant']) ?  (int) $data['variant'] : 1,
            "steps" => 30,
        ];

        return $this->makeCurlRequest();
    }

    /**
     * prepare promt
     *
     * @param array $data
     * @return [type]
     */
    public function promt($data)
    {
        return filled(request('file')) || request('file') == 'undefined' ? $this->imageToImage($data) : $this->generalPromt($data);
    }

    /**
     * Curl Request
     * @return [type]
     */
    public function response($response)
    {
        if (isset($response['artifacts'])) {
            return $this->save($response);
        } else {
            return [
                'response' => $response['message'],
                'status' => 'failed'
            ];
        }

    }

    /**
     * Curl Request
     *
     * @return [type]
     */
    public function makeCurlRequest()
    {
        $curl = curl_init();

        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->options()['url'],
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => config('openAI.ssl_verify_host'),
            CURLOPT_SSL_VERIFYPEER => config('openAI.ssl_verify_peer'),
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            
            CURLOPT_POSTFIELDS => $this->options()['data'],
            CURLOPT_HTTPHEADER => array(
                "Content-Type: " . $this->options()['type'],
                "Authorization: Bearer " . apiKey('stablediffusion')
            ),
        ));
        
        // Make API request
        $response = curl_exec($curl);

        // Close cURL session
        curl_close($curl);
        $response = json_decode($response, true);
        return $this->response($response);
    }

    /**
     * get options
     *
     * @return array
     */
    public function options()
    {
        if (filled(request('file')) || request('file') == 'undefined') {
            return [
                'url' => $this->Imageurl,
                'type' => 'multipart/form-data',
                'data' => $this->imageToImagePromt,
            ];
        } else {
            return [
                'url' => $this->url,
                'type' => 'application/json',
                'data' => json_encode($this->promtText)
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
        $totalImages = count($data['artifacts'][0]);
        $responseArray['created-at'] = time();
        $id = null;
        
        foreach ($data['artifacts'] as $key => $value) {
            $image = base64_decode($value['base64']);
            $this->upload($image);
            $slug = $totalImages > 0 ? $this->imageService->createSlug(request('promt') . $key) : $this->imageService->createSlug(request('promt'));
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
                'libraries' => 'Stablediffusion',
                'meta' => json_encode($responseArray),
            ];

            $id = $this->imageService->storeData($images);
            $urlWithParams = objectStorage()->url('user/image-gallery?slug=' . $slug);
            $imageNames[] = [
                'id' => $id,
                'url' => $this->imageService->storagePath() . DIRECTORY_SEPARATOR . $this->imageService->imageName,
                'slug_url' => $urlWithParams,
                'name' => $name,
                'size' => request('resulation'),
                'artStyle' => request('artStyle'),
                'lightingStyle' => request('lightingStyle'),
                'created_at' => now(),
                'libraries' => 'Stablediffusion'
            ];
        }
        
       return $imageNames;
    }

    /**
     * Store Images
     * @param mixed $data
     *
     * @return [type]
     */
    public function upload($url)
    {
        $filename = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(" ", "_",  $this->imageService->createName(request('promt'))));
        $filename = md5(uniqid()) . "." . "jpg";
        $this->imageService->imageName = $filename;
        $image = objectStorage()->put($this->imageService->uploadPath() . DIRECTORY_SEPARATOR . $filename, $url);
        $this->imageService->makeThumbnail($this->imageService->imageName);
        return $image;
    }

 }
