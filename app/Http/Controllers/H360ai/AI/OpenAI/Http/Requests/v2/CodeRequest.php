<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;

class CodeRequest extends FormRequest
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
            'prompt' => 'required',
            'provider' => 'required',
            'language' => 'sometimes|nullable',
            'codeLevel' => 'sometimes|nullable',
            'model' => 'sometimes|nullable'
        ];
    }

    
    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'provider' => __('Provider is not available. Please contact with Admin')
        ];
    }

}
