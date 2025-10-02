<?php 

namespace Modules\OpenAI\AiProviders\Clipdrop\Responses;

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
        if ($this->response['code'] == 200) {
            $this->images[] = Image::make($this->response['body']);
        } elseif (in_array($this->response['code'], [400, 401, 402, 403, 404, 429, 500])) {
            $this->handleException(json_decode($this->response['body'])->error);
        } else {
            $this->handleException(__('There was an issue generating your AI Image, please try again or contact with the adminstration.'));
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
