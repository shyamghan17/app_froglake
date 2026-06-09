<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingCustomPageRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $customPageId = $this->route('page')->id ?? null;

        return [
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'required|string|max:50|unique:booking_custom_pages,slug,' . $customPageId . ',id,created_by,' . creatorId(),
            'page_header' => 'nullable|string|max:255',
            'page_header_description' => 'nullable|string',
            'content' => 'nullable|string',
            'meta_data' => 'nullable|array',
            'is_active' => 'boolean'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('Page title is required'),
            'title.max' => __('Page title cannot exceed 255 characters'),
            'page_header.max' => __('Page header cannot exceed 255 characters'),
            'slug.unique' => __('Slug already exists, please use another.')
        ];
    }
}