<?php

namespace Modules\OpenAI\Transformers\Api\v2\Image;

use Modules\OpenAI\Transformers\Api\v2\Image\SingleImageResources;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OpenAI\Entities\Archive;

class ImageReplyResources extends JsonResource
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
            'parent_id' => $this->parent_id,
            'title' => $this->title,
            'provider' => $this->provider,
            'type' => $this->type,
            'created_at' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->diffForHumans(),
            'images' => SingleImageResources::collection(Archive::with('metas')->where('parent_id', $this->id)->get()),
            'balance_reduce_type' => $this->balance_reduce_type,
            'metas' => $this->checkUrl()
        ];
    }

    /**
     * Check and modify URLs within the metadata.
     *
     * @return array The updated metadata with valid URLs for 'images_urls' and 'url' keys.
     */
    public function checkUrl() {
        $metas = $this->metas->pluck('value', 'key');
    
        foreach ($metas as $key => $meta) {
            if ($key === 'images_urls' && is_array($meta)) {
                foreach ($meta as $index => $data) {
                    $meta[$index] = objectStorage()->url(str_replace("\\", "/", $data));
                }
                $metas[$key] = $meta;
            } elseif ($key === 'url') {
                $metas[$key] = objectStorage()->url(str_replace("\\", "/", $data));
            }
        }

        return $metas;
    }
    
}
