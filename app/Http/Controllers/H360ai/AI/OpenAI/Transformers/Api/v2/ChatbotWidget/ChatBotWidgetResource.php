<?php

namespace Modules\OpenAI\Transformers\Api\v2\ChatbotWidget;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OpenAI\Entities\{
    Archive,
    FeaturePreference
};
use Modules\OpenAI\Services\v2\ChatBotWidgetService;

class ChatBotWidgetResource extends JsonResource
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
            'user_id' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name
            ],
            'name' => $this->name,
            'code' => $this->code,
            'role' => $this->role,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'meta' => $this->decodeMetaValues($this->metas),
            'total_conversation' => $this->totalConversation($this->code),
            'deleted_at' => is_null($this->deleted_at) ? false : true,
        ];
    }

    /**
     * Decode JSON meta values.
     *
     * @param  \Illuminate\Support\Collection  $metas
     * @return \Illuminate\Support\Collection
     */
    protected function decodeMetaValues($metas)
    {
        return $metas->pluck('value', 'key')->map(function ($value, $key) {
            if (isset($value['url'])) {
                $url = !$value['is_delete'] ? (new ChatBotWidgetService())->chatbotSettings($key) : $value['url'];
                $value['url'] = objectStorage()->url($url);
            }
            return $value;
        });
    }

    /**
     * Calculate the total number of unique conversations associated with a specific chatbot code.
     *
     * @param string $code The unique code associated with the chatbot conversations.
     *
     * @return int The total number of unique conversations.
     */
    protected function totalConversation(string $code): int
    {
        $conversationIds = Archive::whereType('chatbot_chat')
            ->whereHas('metas', function ($query) use ($code) {
                $query->where(['key' => 'chatbot_code', 'value' => $code]);
            })
            ->pluck('id')
            ->unique()
            ->toArray();

        return count($conversationIds);
    }
}
