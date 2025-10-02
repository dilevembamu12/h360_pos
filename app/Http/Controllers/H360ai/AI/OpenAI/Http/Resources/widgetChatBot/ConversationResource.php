<?php

namespace Modules\OpenAI\Http\Resources\widgetChatBot;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OpenAI\Transformers\Api\v2\ChatbotWidget\ChatDetailsResource;
use Modules\OpenAI\Transformers\Api\v2\ChatbotWidget\ChatBotWidgetResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Str;

class ConversationResource extends JsonResource
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
            'id' => isset($this['chat_conversation_id']) ? $this['chat_conversation_id'] : $this['id'],
            'uuid' => Str::uuid(),
            'title' => isset($this['title']) ? $this['title'] : $this['name'],
            'type' => $this['type'],
            'created_at' => timeToGo($this->created_at, false, 'ago'),
            'user' => new UserResource($this->user),
            'bot_details' => new ChatBotWidgetResource($this->whenLoaded('chatbotWidget')),
            'child' => ChatDetailsResource::collection($this->conversationChilds),
        ];
    }
}
