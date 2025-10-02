<?php

namespace Modules\OpenAI\Transformers\Api\v2\AiVideo;

use Illuminate\Http\Resources\Json\JsonResource;

class AiVideoDetailsResource extends JsonResource
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
            'id' => $this['video']->id,
            'type' => $this['video']->type,
            'provider' => $this['video']->provider,
            'expense' => $this['video']->expense,
            'expense_type' => $this['video']->expense_type,
            'created_at' => timeToGo($this['video']->created_at, false, 'ago'),
            'updated_at' => timeToGo($this['video']->updated_at, false, 'ago'),
            'user' => [
                'id' => $this['video']->user?->id,
                'name' => $this['video']->user?->name,
            ],
            'meta' => $this['video']->metas->pluck('value', 'key'),
            'subscription' => $this['balance'],
        ];
    }
}
