<?php

namespace Modules\OpenAI\Http\Requests;

use App\Rules\ImageFileInputRule;
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
            'promt' => 'required',
            'variant' => 'sometimes|integer|nullable',
            'parent_id' => 'sometimes|integer|nullable',
            'file' => ['sometimes','required', 'mimes:jpeg,png,jpg,gif', new ImageFileInputRule()]
        ];
    }

}
