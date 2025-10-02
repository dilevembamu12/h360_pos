<?php

namespace Modules\OpenAI\Http\Requests\v2;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckChatbotFile;

class ChatBotWidgetTrainingRequest extends FormRequest
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
            'type' => 'required|in:url,file,text',
            'url' => ['required_if:type,url','array'],
            'url.*' => ['url'],
            'file' => ['required_if:type,file', 'array', new CheckChatbotFile()],
            'file.*' => ['mimes:pdf,doc,docx,txt'],
            'text' => ['required_if:type,text', 'string']
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'file.*.mimes' => __('Each document must be in PDF, DOC, DOCX or TXT format.'),
            'url.*.url' => __('Each URL must be a valid URL.'),
        ];
    }
}
