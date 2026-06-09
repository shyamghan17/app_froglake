<?php

namespace Workdo\Rotas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRotasShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shift_name' => 'required|max:100',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_start_time' => 'required|date_format:H:i',
            'break_end_time' => 'required|date_format:H:i',
            'is_night_shift' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'shift_name.required' => __('Shift name is required.'),
            'shift_name.max' => __('Shift name cannot exceed 100 characters.'),
            'start_time.required' => __('Start time is required.'),
            'start_time.date_format' => __('Start time must be in HH:MM format.'),
            'end_time.required' => __('End time is required.'),
            'end_time.date_format' => __('End time must be in HH:MM format.'),
            'break_start_time.required' => __('Break start time is required.'),
            'break_start_time.date_format' => __('Break start time must be in HH:MM format.'),
            'break_end_time.required' => __('Break end time is required.'),
            'break_end_time.date_format' => __('Break end time must be in HH:MM format.'),
            'is_night_shift.boolean' => __('Night shift status must be true or false.'),
        ];
    }
}