<?php

namespace Modules\OpenAI\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Modules\OpenAI\Entities\Folder;
use Illuminate\Foundation\Http\FormRequest;

class FolderStoreRequest extends FormRequest
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (!empty($this->name)) {
            $this->merge(['slug' => $this->generateSlug()]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:191'],
            'slug' => ['required', Rule::unique('folders', 'slug')->ignore($this->id)],
            'user_id'=> ['required', 'integer'],
            'parent_id'=> ['nullable', 'integer'],
            'folder_id' => ['nullable', 'integer'],
        ];
    }

    /**
     * Generate slug
     */
    public function generateSlug(): string
    {
        $slug = Str::slug($this->name);

        if (!empty($this->id) && is_numeric($this->id) && $model = Folder::find($this->id)) {
            if ($model->name === $this->name) {
                $slug = $model->slug;
            }

            if (Folder::whereKeyNot([$this->id])->whereSlug($slug)->exists()) {
                $slug = $this->generateUniqueSlug($slug);
            }
        } else if (Folder::whereSlug($slug)->exists()) {
            $slug = $this->generateUniqueSlug($slug);
        }

        return $slug;
    }

    /**
     * Generate unique slug
     *
     * @param  string  $slug
     */
    public function generateUniqueSlug($slug): string
    {
        return $slug . '-' . substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5);
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.min' => __('Name should be at least of 2 characters.'),
            'name.max' => __('Name should not be more than 191 characters.'),
        ];
    }

}
