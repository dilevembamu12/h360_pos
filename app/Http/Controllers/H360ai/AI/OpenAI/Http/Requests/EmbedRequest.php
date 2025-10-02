<?php

namespace Modules\OpenAI\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\OpenAI\Services\v2\FeaturePreferenceService;

class EmbedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $this->checkAndMergeProviderModel();

        return [
            'chunk' => 'sometimes|integer',
            'type' => 'required|in:url,file',
            'url' => ['required_if:type,url', 'url'],
            'file' => ['required_if:type,file', 'array'],
            'file.*' => ['mimes:pdf,doc,docx'],
            'provider' => 'required',
            'model' => 'required',
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'provider.required' => __('Embedding provider is not available. Please contact with the adminstration.'),
            'file.*.required' => __('Each document file is required.'),
            'file.*.mimes' => __('Each document must be in PDF, DOC, or DOCX format.'),
        ];
    }

    /**
     * Check and merge provider and model data from FeaturePreferenceService.
     *
     * @return void
     */
    private function checkAndMergeProviderModel(): void
    {
        $data = (new FeaturePreferenceService())->processData('ai_doc_chat');
        if (!empty($data) && isset($data['user_access_disable']) && $data['user_access_disable'] === 'on') {

            $this->merge([
                'provider' => $data['provider'],
                'model' => $data['model'],
            ]);

            request()->merge([
                'provider' => $data['provider'],
                'model' => $data['model']
            ]);
        }
    }
}
