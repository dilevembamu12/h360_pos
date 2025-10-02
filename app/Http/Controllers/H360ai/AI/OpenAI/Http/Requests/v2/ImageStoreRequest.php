<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;

class ImageStoreRequest extends FormRequest
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
            'file' => ['sometimes','required', 'mimes:jpeg,png,jpg,gif']
        ];
    }

    public function messages()
    {
        return [
            'provider.required' => __('Provider is required for generate image.'),
        ];
    }
}
