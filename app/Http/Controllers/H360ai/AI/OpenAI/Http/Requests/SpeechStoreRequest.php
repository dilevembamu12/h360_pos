<?php

namespace Modules\OpenAI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\{CheckValidFile};

class SpeechStoreRequest extends FormRequest
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
            'file' => ['required', new CheckValidFile(getFileExtensions(6))],
        ];
    }

    /**
     * Get the custom messages for the validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'file.required' => __('The audio file is required.'),
        ];
    }

}
