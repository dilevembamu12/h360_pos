<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckValidFile;

class VisionUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3'
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages()
    {
        return [
            'name.required' => __('Conversation name is required!'),
            'name.min' => __('The conversation name must be at least :x characters long.', ['x' => 3]),
        ];
    }
}
