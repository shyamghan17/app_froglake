<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingBusinessHoursRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->can('create-booking-business-hours');
    }

    public function rules()
    {
        return [
            'business_hours' => 'required|array',
            'business_hours.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'business_hours.*.is_closed' => 'required|boolean',
            'business_hours.*.time_slots' => 'nullable|array',
            'business_hours.*.time_slots.*.open' => 'required_if:business_hours.*.is_closed,false|date_format:H:i',
            'business_hours.*.time_slots.*.close' => 'required_if:business_hours.*.is_closed,false|date_format:H:i|after:business_hours.*.time_slots.*.open'
        ];
    }

    public function messages()
    {
        return [
            'business_hours.*.time_slots.*.open.required_if' => 'Opening time is required when the day is not closed.',
            'business_hours.*.time_slots.*.close.required_if' => 'Closing time is required when the day is not closed.',
            'business_hours.*.time_slots.*.close.after' => 'Closing time must be after opening time.',
            'business_hours.*.time_slots.*.open.date_format' => 'Opening time must be in HH:MM format.',
            'business_hours.*.time_slots.*.close.date_format' => 'Closing time must be in HH:MM format.'
        ];
    }
}