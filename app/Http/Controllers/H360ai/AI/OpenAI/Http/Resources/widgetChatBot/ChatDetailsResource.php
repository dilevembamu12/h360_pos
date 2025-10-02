<?php

namespace Modules\OpenAI\Http\Resources\widgetChatBot;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OpenAI\Transformers\Api\v2\ChatbotWidget\ChatBotWidgetResource;
use App\Http\Resources\UserResource;

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
            'user' => new UserResource($this->whenLoaded('user')),
            'bot_details' => new ChatBotWidgetResource($this->whenLoaded('chatbotWidget')),
            'meta' => $this->metas->pluck('value', 'key')
        ];
    }
}
