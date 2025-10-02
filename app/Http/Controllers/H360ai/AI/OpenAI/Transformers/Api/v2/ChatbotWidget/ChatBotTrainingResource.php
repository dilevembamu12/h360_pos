<?php

namespace Modules\OpenAI\Transformers\Api\v2\ChatbotWidget;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Collection;

class ChatBotTrainingResource extends JsonResource
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
            'file_url' => empty($this->chatBotEmbedFileUrl()) ? $this->original_name : $this->chatBotEmbedFileUrl(),
            'created_at' => timeToGo($this->created_at, false, 'ago'),
            'updated_at' => timeToGo($this->updated_at, false, 'ago'),
            'meta' => $this->metaCheck($this->metas),
            'user' => new UserResource($this->user),
            'child' => ChatBotTrainingResource::collection($this->childs),
        ];
    }

    /**
     * Decode JSON meta values.
     *
     * @param  Collection  $metas
     * @return Collection
     */
    protected function metaCheck(Collection $metas): Collection
    {
        return $metas->pluck('value', 'key')->map(function ($value, $key) {
            if ($key === 'last_trained') {
                return $value == 'N\A' ? $value : timeToGo($value, false, 'ago');
            }

            return $value;
        });
    }
}
