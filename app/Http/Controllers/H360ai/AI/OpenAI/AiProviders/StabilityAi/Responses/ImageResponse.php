<?php 

namespace Modules\OpenAI\AiProviders\StabilityAi\Responses;

use Modules\OpenAI\Contracts\Responses\ImageResponseContract;
use Intervention\Image\Facades\Image;
use Exception;

class ImageResponse implements ImageResponseContract
{
    public $response;
    public $images = [];

    public function __construct($aiResponse) 
    {
        $this->response = $aiResponse;
        $this->process();
    }

    public function process()
    {
        $images = json_decode($this->response['body']);
    
        if ($this->response['code'] == 200) {
            foreach ($images->artifacts as  $image) {
                $decodedImage = base64_decode($image->base64);
                $this->images[] = Image::make($decodedImage);
            }
        } elseif (in_array($this->response['code'], [400, 401, 403, 404, 429, 500])) {
            $this->handleException($images->message);
        } else {
            $message = isset($images->message) ? $images->message : __('Something went wrong, please try again.');
            $this->handleException($message);
        }
        
    }

    public function images(): array
    {
        return $this->images;
    }

    public function response(): mixed
    {
        return $this->response;
    }

    public function handleException(string $message): Exception
    {
        throw new \Exception($message);
    }
}