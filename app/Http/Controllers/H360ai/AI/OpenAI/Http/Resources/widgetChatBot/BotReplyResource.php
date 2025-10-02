<?php

namespace Modules\OpenAI\Http\Resources\widgetChatBot;

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
            'chatbot_chat_id' => request('chatbot_chat_id') ?? $this->parent_id,
            'type' => $this->type,
            'created_at' => timeToGo($this->created_at, false, 'ago'),
            'meta' => $this->meta_data
        ];
    }
}
