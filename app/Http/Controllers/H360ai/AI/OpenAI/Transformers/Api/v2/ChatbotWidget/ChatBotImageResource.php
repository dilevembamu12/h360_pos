<?php

namespace Modules\OpenAI\Transformers\Api\v2\ChatbotWidget;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class ChatBotImageResource extends JsonResource
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
            'meta' => $this->filterImage($this->metas)
        ];
    }

    /**
     * Decode JSON meta values.
     *
     * @param  Collection  $metas
     * @return Collection
     */
    protected function filterImage(Collection $metas): Collection
    {
        return $metas->pluck('value', 'key')->map(function ($value, $key) {
            if ( ($key === 'image' || $key === 'floating_image') && isset($value['url'])) {
                $value['url'] = objectStorage()->url($value['url']);
            }
            return $value;
        })->filter(function ($value, $key) {
            return ($key === 'image' || $key === 'floating_image');
        });
    }
}
