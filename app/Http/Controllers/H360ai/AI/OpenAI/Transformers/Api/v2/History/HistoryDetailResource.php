<?php

namespace Modules\OpenAI\Transformers\Api\v2\History;

use App\Http\Resources\UserResource;
use Modules\OpenAI\Entities\Archive;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\OpenAI\Transformers\Api\v2\Image\SingleImageResources;
use Modules\OpenAI\Transformers\Api\v2\Embedding\EmbedFileResource;
use Modules\OpenAI\Transformers\Api\v2\AiChat\ChatBotResource;

class HistoryDetailResource extends JsonResource
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
            'updated_at' => timeToGo($this->updated_at, false, 'ago'),
            'images' => $this->type == 'image_chat' ? SingleImageResources::collection(Archive::with('metas')->where('parent_id', $this->id)->get()) : null,
            'user' => new UserResource($this->user),
            'meta' => $this->checkUrl(),
            'files' => EmbedFileResource::collection($this->file()),
            'bot_details' => new ChatBotResource($this->whenLoaded('chatbot'))
        ];
    }

    /**
     * Check and modify URLs within the metadata.
     *
     * @return array The updated metadata with valid URLs for 'images_urls' and 'url' keys.
     */
    public function checkUrl() {
        $metas = $this->metas->pluck('value', 'key');
    
        foreach ($metas as $key => $meta) {
            if ( ($key === 'images_urls' || $key === 'user_files') && is_array($meta)) {
                foreach ($meta as $index => $data) {
                    $meta[$index] = objectStorage()->url(str_replace("\\", "/", $data));
                }
                $metas[$key] = $meta;
            } elseif ($key === 'url') {
                $metas[$key] = objectStorage()->url(str_replace("\\", "/", $data));
            }
        }

        return $metas;
    }
}
