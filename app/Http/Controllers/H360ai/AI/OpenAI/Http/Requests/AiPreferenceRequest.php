<?php

namespace Modules\OpenAI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckProvider;

class AiPreferenceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'short_desc_length' => ['required', 'integer', 'regex:/^(?:[1-9]|[1-9][0-9]{1,2}|1000)$/'],
            'long_desc_length' => ['required', 'integer', 'regex:/^(?:[1-9]|[1-9][0-9]{1,2}|1000)$/'],
            'ai_model' => 'required',
            'max_token_length' => 'required|integer',
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
}
