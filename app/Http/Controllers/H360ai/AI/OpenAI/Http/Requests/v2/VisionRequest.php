<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckValidFile;

class VisionRequest extends FormRequest
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
            'chatId' => 'sometimes|integer|nullable',
            'botId' => 'sometimes|integer|nullable',
            'file' => ['sometimes', 'required', 'array'],
            'provider' => 'required',
            'model' => 'required'
        ];
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
