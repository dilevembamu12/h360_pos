<?php

namespace Modules\OpenAI\Transformers\Api\v2\Image;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OpenAI\Entities\Archive;

class ImageChatResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'slug_url' => url('user/image-gallery?slug=' . $this->slug), 
            'url' => $this->url,
            'type' => $this->type
        ];
    }
}
