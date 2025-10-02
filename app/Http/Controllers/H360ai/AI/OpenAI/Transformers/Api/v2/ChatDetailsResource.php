<?php

namespace Modules\OpenAI\Transformers\Api\v2;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ] : null,
            'meta_data' => [
                $this->checkUrl()
            ]
        ];
    }

    /**
     * Check and modify URLs within the metadata.
     *
     * @return array The updated metadata with valid URLs for 'user_files' keys.
     */
    public function checkUrl() {
        $metas = $this->metas->pluck('value', 'key');
    
        foreach ($metas as $key => $meta) {
            if ($key === 'user_files' && is_array($meta)) {
                foreach ($meta as $index => $data) {
                    $meta[$index] = objectStorage()->url(str_replace("\\", "/", $data));
                }
                $metas[$key] = $meta;
            }
        }
        return $metas;
    }
}
