<?php

namespace Modules\OpenAI\Transformers\Api\v2\Embedding;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EmbedFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $parts = explode('\\', $this->name);

        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => end($parts),
            'original_name' => $this->original_name,
            'type' => $this->type,
            'content' => $this->content,
            'file_url' => $this->embedFileUrl(),
            'created_at' => formatDate($this->created_at),
            'updated_at' => formatDate($this->created_at),
            'user' => new UserResource($this->user),
            'meta' => $this->metas->pluck('value', 'key'),
            'child' => EmbedResource::collection($this->childs),
        ];
    }
}
