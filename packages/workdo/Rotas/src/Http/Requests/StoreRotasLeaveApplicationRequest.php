<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRotasLeaveApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => __('Employee is required.'),
            'employee_id.exists' => __('Selected employee is invalid.'),
            'leave_type_id.required' => __('Leave type is required.'),
            'leave_type_id.exists' => __('Selected leave type is invalid.'),
            'start_date.required' => __('Start date is required.'),
            'start_date.date' => __('Start date must be a valid date.'),
            'end_date.required' => __('End date is required.'),
            'end_date.date' => __('End date must be a valid date.'),
            'end_date.after_or_equal' => __('End date must be after or equal to start date.'),
            'reason.required' => __('Reason is required.'),
            'reason.string' => __('Reason must be a string.'),
        ];
    }
}