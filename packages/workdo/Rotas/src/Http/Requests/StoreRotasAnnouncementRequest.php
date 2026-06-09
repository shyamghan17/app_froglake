<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRotasAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'announcement_category_id' => 'required|exists:announcement_categories,id',
            'departments' => 'required|array',
            'departments.*' => 'exists:departments,id',
            'description' => 'required',
            'priority' => 'required',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('Title is required.'),
            'title.string' => __('Title must be a string.'),
            'title.max' => __('Title cannot exceed 255 characters.'),
            'announcement_category_id.exists' => __('Selected announcement category is invalid.'),
            'announcement_category_id.required' => __('Announcement category is required.'),
            'departments.required' => __('Departments are required.'),
            'departments.array' => __('Departments must be an array.'),
            'departments.*.exists' => __('Selected department is invalid.'),
            'description.required' => __('Description is required.'),
            'priority.required' => __('Priority is required.'),
            'start_date.required' => __('Start date is required.'),
            'start_date.date' => __('Start date must be a valid date.'),
            'start_date.after_or_equal' => __('Start date must be today or later.'),
            'end_date.required' => __('End date is required.'),
            'end_date.date' => __('End date must be a valid date.'),
            'end_date.after' => __('End date must be after start date.'),
        ];
    }
}
