<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;

class TemplateRequest extends FormRequest
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
            'questions' => 'required',
            'useCase' => 'required',
            'language' => 'nullable',
            'variant' => 'nullable',
            'temperature' => 'nullable',
            'model' => 'nullable',
            'tone' => 'nullable',
            'creativity_level' => 'nullable',
            'stream-data' => ['nullable']
        ];
    }

}
