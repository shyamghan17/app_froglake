<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|max:200',
            'slug' => 'required|string|max:50|unique:beauty_custom_pages,slug,NULL,id,created_by,' . creatorId(),
            'description' => 'nullable|max:500',
            'contents' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'Slug already exists, please choose a different one.'
        ];
    }
}