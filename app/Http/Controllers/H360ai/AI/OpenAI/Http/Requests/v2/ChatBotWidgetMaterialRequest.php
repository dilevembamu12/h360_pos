<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;

class ChatBotWidgetMaterialRequest extends FormRequest
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
            'embeded_id' => 'required|array',
            'embedding_provider' => 'nullable|string',
            'embedding_model' => 'nullable|string',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'embeded_id.required' => __('Please ensure that materials are provided.'),
        ];
    }
}
