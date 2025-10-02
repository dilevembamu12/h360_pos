<?php

namespace Modules\OpenAI\Transformers\Api\v2\History;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
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
            'uuid' => $this->unique_identifier,
            'title' => $this->title,
            'type' => $this->type,
            'created_at' => $this->created_at->diffForHumans(),
        ];
    }
}
