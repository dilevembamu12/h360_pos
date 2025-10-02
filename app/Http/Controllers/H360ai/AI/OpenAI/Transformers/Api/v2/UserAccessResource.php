<?php

namespace Modules\OpenAI\Transformers\Api\v2;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAccessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        if (empty($this->resource) || auth()->user()->role()->type == 'admin') {
            return [
                "hide_template" => false,
                "hide_image" => false,
                "hide_code" => false,
                "hide_speech_to_text" => false,
                "hide_text_to_speech" => false,
                "hide_long_article" => false,
                "hide_chat" => false,
                "hide_plagiarism" => false,
                "hide_aichatbot" => false,
                "hide_video" => false,
            ];
        }

        foreach ($this->resource as $key => $value) {
            $data[$key] = $value === "1" ? true : false;
        }

        return $data;
    }
}
