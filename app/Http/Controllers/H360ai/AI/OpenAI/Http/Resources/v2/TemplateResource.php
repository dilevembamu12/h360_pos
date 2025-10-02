<?php

namespace Modules\OpenAI\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class TemplateResource extends JsonResource
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
            'content' => $this->content,
            'expense' => $this->expense,
            'expense_type' => $this->expense_type,
            'bookmark' => $this->checkBookmark(),
            'created_at' => timeZoneFormatDate($this->created_at),
            'updated_at' => timeZoneFormatDate($this->updated_at),
            'user' => new UserResource($this->user),
            'use_case' => $this->useCase,
            'meta' => $this->metas->pluck('value', 'key'),
        ];
    }

    /**
     * Check if the current template is bookmarked by the authenticated user.
     *
     * @return bool True if the template is bookmarked, false otherwise.
     */
    private function checkBookmark()
    {
        if (is_null(auth()->user()->document_bookmarks_openai)) {
            return false;
        }

        return in_array($this->id, auth()->user()->document_bookmarks_openai);
    }
}
