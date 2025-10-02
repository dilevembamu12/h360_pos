<?php

namespace Modules\OpenAI\Http\Resources\v2;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class CodeResource extends JsonResource
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
            'created_at' => timeZoneFormatDate($this->created_at),
            'updated_at' => timeZoneFormatDate($this->updated_at),
            'user' => new UserResource($this->user),
            'meta' => $this->metas->pluck('value', 'key'),
        ];
    }
}
