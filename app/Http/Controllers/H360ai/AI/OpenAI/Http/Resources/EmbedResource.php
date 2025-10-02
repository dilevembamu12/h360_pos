<?php

namespace Modules\OpenAI\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EmbedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * 
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
            'file_url' => empty($this->embedFileUrl()) ? $this->original_name : $this->embedFileUrl(),
            'created_at' => timeToGo($this->created_at, false, 'ago'),
            'updated_at' => timeToGo($this->updated_at, false, 'ago'),
            'meta' => $this->metas->pluck('value', 'key'),
            'user' => new UserResource($this->user),
            'child' => EmbedResource::collection($this->childs),
        ];
    }
}
