<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ToggleBookmarkRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'content_id' => [Rule::exists('archives', 'id'), 'required', 'numeric', 'gt:0'],
            'toggle_state' => 'required|in:true,false'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
