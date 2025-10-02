<?php

namespace Modules\OpenAI\Transformers\Api\v2\AiChat;

use Modules\OpenAI\Transformers\Api\v2\AiChat\ChatBotResource;
use Illuminate\Http\Resources\Json\JsonResource;
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->user),
            'meta' => $this->metas->pluck('value', 'key'),
            'bot_details' => new ChatBotResource($this->whenLoaded('chatbot'))
        ];
    }
}
