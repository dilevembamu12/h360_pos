<?php

namespace Modules\OpenAI\Transformers\Api\v2\SpeechToText;

use Illuminate\Http\Resources\Json\JsonResource;

class SpeechToTextDetailsResource extends JsonResource
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
            'type' => $this->type,
            'title' => trimWords($this->title, 30),
            'content' => $this->title,
            'provider' => $this->provider,
            'expense' => $this->expense,
            'created_at' => timeToGo($this->created_at, false, 'ago'),
            'updated_at' => timeToGo($this->updated_at, false, 'ago'),
            'meta' => $this->metas->pluck('value', 'key'),
        ];
    }
}
