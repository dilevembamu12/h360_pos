<?php

namespace Modules\OpenAI\Transformers\Api\v2\AiChat;

use Illuminate\Http\Resources\Json\JsonResource;

class BotReplyResource extends JsonResource
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
            'chat_id' => $this->parent_id,
            'provider' => $this->provider,
            'expense' => $this->expense,
            'expense_type' => $this->expense_type,
            'type' => $this->type,
            'created_at' => timeToGo($this->created_at, false, 'ago'),
            'updated_at' => timeToGo($this->updated_at, false, 'ago'),
            'meta' => $this->metas->pluck('value', 'key'),
            'bot_details' => new ChatBotResource($this->whenLoaded('chatbot')),
        ];
    }
}
