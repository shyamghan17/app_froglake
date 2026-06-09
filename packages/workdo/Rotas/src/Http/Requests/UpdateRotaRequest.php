<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'notes' => 'nullable|string',
            'schedule_data' => 'required|array',
            'is_published' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => __('Title is required.'),
            'title.string' => __('Title must be a string.'),
            'title.max' => __('Title cannot exceed 255 characters.'),
            'start_date.required' => __('Start date is required.'),
            'start_date.date' => __('Start date must be a valid date.'),
            'end_date.required' => __('End date is required.'),
            'end_date.date' => __('End date must be a valid date.'),
            'end_date.after_or_equal' => __('End date must be after or equal to start date.'),
            'branch_id.exists' => __('Selected branch is invalid.'),
            'department_id.exists' => __('Selected department is invalid.'),
            'designation_id.exists' => __('Selected designation is invalid.'),
            'schedule_data.required' => __('Schedule data is required.'),
            'schedule_data.array' => __('Schedule data must be an array.'),
            'is_published.boolean' => __('Published status must be true or false.'),
        ];
    }
}