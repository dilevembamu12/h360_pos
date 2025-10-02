<?php

/**
 * @package Clipdrop
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 01-11-2023
 */

namespace Modules\OpenAI\Libraries;


 class Clipdrop
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
    protected $removeTextUrl;
    /**
     * Remove background URL
     *
     * @var string
     */ 
    protected $reimagineUrl;
    /**
     * Remove background URL
     *
     * @var string
     */
    
    protected $replaceBackgorundUrl;
    /**
     * Remove background URL
     *
     * @var string
     */
    protected $sketchToImage;
    /**
     * Remove background URL
     *
     * @var string
     */
    protected $removeBackgorundUrl;
    /**
     * Promt
     *
     * @var string
     */
    protected $promtText;

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
        $this->imageService = $imageService;
        $this->url = "https://clipdrop-api.co/text-to-image/v1";
        $this->removeTextUrl = "https://clipdrop-api.co/remove-text/v1";
        $this->removeBackgorundUrl = "https://clipdrop-api.co/remove-background/v1";
        $this->replaceBackgorundUrl = "https://clipdrop-api.co/replace-background/v1";
        $this->sketchToImage = "https://clipdrop-api.co/sketch-to-image/v1/sketch-to-image";
        $this->reimagineUrl = "https://clipdrop-api.co/reimagine/v1/reimagine";
    }

    /**
     * prepare file
     * @return [type]
     */

     public function prepareFile()
     {
        $uploadedFile = request('file');
        $originalFileName = $uploadedFile->getClientOriginalName();
        return new \CURLFile($uploadedFile->getRealPath(), $uploadedFile->getMimeType(), $originalFileName);
     }

    /**
     * prepare promt
     *
     * @param array $data
     * @return void
     */
    public function promt($data)
    {
        
        $promptData = [
            'prompt' => 'Generate an image featuring the prompt ' . $data['promt'] . ' in a ' . $data['lightingStyle'] . ' mode, and apply the art style ' . $data['artStyle'] . ' to the composition',
            'image_file' => isset($data['file']) ? $this->prepareFile() : null
        ];

        switch($data['service']) {
            case 'sketch-to-image':
                $promptData['sketch_file'] = $promptData['image_file'];
                unset($promptData['image_file']);
                break;
            case 'remove-background':
                unset($promptData['prompt']);
                break;
            case 'reimagine':
                unset($promptData['prompt']);
                break;
            case 'default':
                unset($promptData['prompt']);
                break;
        }

        $this->promtText = $promptData;

        return $this->makeCurlRequest();
    }

    /**
     * Curl Request
     * @return [type]
     */
    public function response($image, $response)
    {
        if ($response == 'Success') {
            return $this->save($image);
        } else {
            return [
                'response' => $response,
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
                "x-api-key: " . apiKey('clipdrop')
            ),
        ));
        
        $image = curl_exec($curl);

         // Get message according to the api response code
        $curlStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return $this->response($image, $this->statusCodeMessage($curlStatusCode));
    }


    /**
     * Check api status code
     * @param mixed $code
     * 
     * @return [type]
     */
    public function statusCodeMessage($code)
    {
        if (array_key_exists($code, $this->statusCode())) {
            return $this->statusCode()[$code];
        }

        return false;
    }
    
    /**
     * Api responce code with message
     * @return [type]
     */
    public function statusCode()
    {
        return [
            '200' => 'Success',
            '400' => 'Request is malformed or incomplete',
            '401' => 'Missing api key',
            '402' => 'Your account has no remaining credits, you can buy more in your account page',
            '403' => 'Invalid or revocated api key',
            '406' => 'Accept header not acceptable',
            '429' => 'Too many requests, blocked by the rate limiter',
            '500' => 'Server Error. Please contact us at contact@clipdrop.co',
        ];
    }
    /**
     * get options
     *
     * @return array
     */
    public function options()
    {
        return [
            'url' => $this->getUrl(),
            'type' => 'multipart/form-data',
            'data' => $this->promtText
        ];
    }

    /**
     * Store Images
     * @param mixed $data
     *
     * @return [type]
     */
    public function save($image)
    {
        $responseArray['created-at'] = time();
        
        $this->upload($image);
        $slug = $this->imageService->createSlug(request('promt'));
        $name = $this->imageService->createName(request('promt'));

         // Optionally check and convert encoding if necessary
         if (!mb_check_encoding($slug, 'UTF-8')) {
            $slug = mb_convert_encoding($slug, 'UTF-8');
        }
        if (!mb_check_encoding($name, 'UTF-8')) {
            $name = mb_convert_encoding($name, 'UTF-8');
        }
        
        $imageFile = $this->imageService->storagePath() . DIRECTORY_SEPARATOR . $this->imageService->imageName;
        $imageSize = getimagesize(str_replace('\\', '/', $imageFile));
       
        $resolution = $imageSize[0] . 'x' . $imageSize[1];
        $id = null;
        $images = [
            'user_id' => auth('api')->user()->id,
            'parent_id' => request('parent_id') ?? $id,
            'name' => $name,
            'original_name' => $this->imageService->imageName,
            'promt' => request('promt'),
            'slug' => $slug,
            'size' => $resolution,
            'art_style' => request('artStyle'),
            'lighting_style' => request('lightingStyle'),
            'libraries' => 'Clipdrop',
            'meta' => json_encode($responseArray),
        ];
        $url = route("user.gallery.show");
        $urlWithParams = url($url . '?slug=' . $slug);
        $id = $this->imageService->storeData($images);

        $imageNames[] = [
            'id' => $id,
            'url' => $this->imageService->storagePath() . DIRECTORY_SEPARATOR . $this->imageService->imageName,
            'slug_url' => $urlWithParams,
            'name' => $name,
            'size' => $resolution,
            'artStyle' => request('artStyle'),
            'lightingStyle' => request('lightingStyle'),
            'created_at' => now(),
            'libraries' => 'Clipdrop',
        ];

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

    /**
     * Get specific service's URL
     *
     * @return string
     */
    public function getUrl()
    {
        switch (request('service')) {
            case 'text-to-image': 
                return $this->url;
            case 'sketch-to-image':
                return $this->sketchToImage;
            case 'replace-background':
                return $this->replaceBackgorundUrl;
            case 'remove-background':
                return $this->removeBackgorundUrl;
            case 'remove-text':
                return $this->removeTextUrl;
            case 'reimagine':
                return $this->reimagineUrl;
        }
    }

 }
