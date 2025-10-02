<?php

namespace Modules\OpenAI\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
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
            'created_at' => $this['created_at'],
        ];
    }
}
