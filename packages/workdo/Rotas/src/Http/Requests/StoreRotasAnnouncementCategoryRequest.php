<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRotasAnnouncementCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'announcement_category' => 'required|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'announcement_category.required' => __('Announcement category is required.'),
            'announcement_category.string' => __('Announcement category must be a string.'),
            'announcement_category.max' => __('Announcement category cannot exceed 255 characters.'),
        ];
    }
}