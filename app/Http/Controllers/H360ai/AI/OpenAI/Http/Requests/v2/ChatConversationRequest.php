<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;
use Modules\OpenAI\Rules\CheckBotExistance;

class ChatConversationRequest extends FormRequest
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
            'chatbot_code' => ['required', new CheckBotExistance],
            'provider' => 'nullable',
            'chatbot_chat_id' => 'nullable',
            'model' => 'nullable',
            'tone' => 'nullable',
            'language' => 'nullable',
            'temperature' => 'nullable',
            'visitor_id' => 'nullable',
        ];
    }
}
