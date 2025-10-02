<?php

namespace Modules\OpenAI\Http\Requests\LongArticle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiResponse;


class LongArticleBlogStoreRequest extends FormRequest
{
    use ApiResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'keywords' => 'required'
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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->unprocessableResponse([
            'response' => $validator->errors()->first(),
            'status' => 'failed',
        ]));
    }
}
