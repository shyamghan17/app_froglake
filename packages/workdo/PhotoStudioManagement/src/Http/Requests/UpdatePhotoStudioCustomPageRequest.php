<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoStudioCustomPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customPageId = $this->route('customPage');

        return [
            'title'              => 'required|string|max:255',
            'contents'           => 'required|string',
            'description'        => 'nullable|string',
            'enable_page_footer' => 'nullable|string|in:on,off',
            'slug'               => 'required|string|max:50|unique:photo_studio_custom_pages,slug,' . $customPageId . ',id,created_by,' . creatorId(),
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'Slug already exists, please use another.',
        ];
    }
}
