<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ChatBotWidgetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'min:1', 'max:30', 'regex:/^[a-zA-Z0-9\s]+$/',
                Rule::unique('chat_bots')->where(function ($query) {
                    return $query->where('user_id', auth()->user()->id)->whereNull('deleted_at')->whereType('widgetChatbot');
                })
            ],
            'theme_color'  => 'required|string',
            'provider'  => 'required|string',
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
