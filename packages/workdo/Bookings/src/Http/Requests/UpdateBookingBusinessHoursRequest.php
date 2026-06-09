<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingBusinessHoursRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('edit-booking-business-hours');
    }

    public function rules()
    {
        return [
            'is_closed' => 'required|boolean',
            'time_slots' => 'nullable|array',
            'time_slots.*.open' => 'required_if:is_closed,false|date_format:H:i',
            'time_slots.*.close' => 'required_if:is_closed,false|date_format:H:i|after:time_slots.*.open'
        ];
    }

    public function messages()
    {
        return [
            'time_slots.*.open.required_if' => 'Opening time is required when the day is not closed.',
            'time_slots.*.close.required_if' => 'Closing time is required when the day is not closed.',
            'time_slots.*.close.after' => 'Closing time must be after opening time.',
            'time_slots.*.open.date_format' => 'Opening time must be in HH:MM format.',
            'time_slots.*.close.date_format' => 'Closing time must be in HH:MM format.'
        ];
    }
}