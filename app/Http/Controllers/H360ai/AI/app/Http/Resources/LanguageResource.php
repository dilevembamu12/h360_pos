<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'direction' => $this->direction,
            'flag' => $this->getFlagUrl()
        ];
    }

    /**
     * Get the URL for the flag SVG image.
    *
    * @return string
    */
    protected function getFlagUrl(): string
    {
        $flagFileName = getSVGFlag($this->short_name) . '.svg';
        return asset("public/datta-able/fonts/flag/flags/4x3/{$flagFileName}");
    }
}
