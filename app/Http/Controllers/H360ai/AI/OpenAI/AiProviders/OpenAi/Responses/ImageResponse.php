<?php 

namespace Modules\OpenAI\AiProviders\OpenAi\Responses;

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
        foreach ($this->response->data as $key => $value) {
            if (!empty($value->url)) {
                $this->images[] =  Image::make($value->url);
            } else {
                $this->images[] =  Image::make($value->b64_json);
            }
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