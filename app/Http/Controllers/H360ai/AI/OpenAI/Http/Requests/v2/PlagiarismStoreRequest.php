<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;

class PlagiarismStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'provider' => 'required',
            'text' => 'required',
        ];
    }

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
     * Custom validation messages
     */
    public function messages()
    {
        return [
            'provider.required' => __('Provider field is required but currently not enabled by system administrator.')
        ];
    }
}
