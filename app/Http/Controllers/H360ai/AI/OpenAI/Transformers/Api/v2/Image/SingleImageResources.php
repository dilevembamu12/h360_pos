<?php

namespace Modules\OpenAI\Transformers\Api\v2\Image;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OpenAI\Entities\Archive;

class SingleImageResources extends JsonResource
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
            'title' => Archive::where('id', $this->parent_id)->first()->title,
            'slug' => $this->slug,
            'slug_url' => url('user/gallery?slug=' . $this->slug),
            'url' =>  objectStorage()->url(str_replace("\\", "/", $this->url)),
            'favorite_image' => $this->checkFavorite(),
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Check if the current image is marked as a favorite by the authenticated user.
     *
     * @return bool True if the image is a favorite, false otherwise.
     */
    private function checkFavorite()
    {
        if (is_null(auth()->user()->image_favorites)) {
            return false;
        }

        return in_array($this->id, auth()->user()->image_favorites);
    }
}
