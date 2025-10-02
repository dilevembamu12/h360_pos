<?php

namespace Modules\OpenAI\Transformers\Api\v2\Image\Gallery;

use Modules\OpenAI\Transformers\Api\v2\Image\SingleImageResources;
use Modules\OpenAI\Transformers\Api\v2\Image\ImageResources;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OpenAI\Entities\Archive;

class ImageGalleryResources extends JsonResource
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
            'image_id' => $this->parent_id,
            'provider' => $this->provider,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'metas' => $this->metas->pluck('value', 'key'),
            'images' => SingleImageResources::collection(Archive::with('metas')->where('parent_id', $this->id)->get()),
            'image' => new ImageResources(Archive::with('metas')->where('id', $this->parent_id)->first())
        ];
    }
}
