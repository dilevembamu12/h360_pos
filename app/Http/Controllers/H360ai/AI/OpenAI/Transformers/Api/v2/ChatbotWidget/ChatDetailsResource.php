<?php

namespace Modules\OpenAI\Transformers\Api\v2\ChatbotWidget;

use App\Http\Resources\UserResource;
use Modules\OpenAI\Entities\ChatBot;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OpenAI\Transformers\Api\v2\ChatbotWidget\ChatBotWidgetResource;

class ChatDetailsResource extends JsonResource
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
            'provider' => $this->provider,
            'expense' => $this->expense,
            'expense_type' => $this->expense_type,
            'created_at' => timeToGo($this->created_at, false, 'ago'),
            'updated_at' => timeToGo($this->created_at, false, 'ago'),
            'user' => new UserResource($this->user),
            'meta' => $this->metas->pluck('value', 'key'),
            'bot_details' => new ChatBotWidgetResource($this->whenLoaded('chatbotWidget'))
        ];
    }
}
