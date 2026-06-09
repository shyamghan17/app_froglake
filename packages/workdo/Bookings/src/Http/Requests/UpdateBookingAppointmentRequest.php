<?php

namespace Workdo\Bookings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingAppointmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'item_id' => 'nullable|exists:product_service_items,id',
            'package_id' => 'nullable|exists:booking_packages,id',
            'staff_id' => 'nullable',
            'customer_id' => 'required|exists:booking_customers,id',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
            'status' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'date.required' => __('The appointment date is required.'),
            'date.date' => __('Please provide a valid date.'),
            'customer_id.required' => __('Please select a customer.'),
            'customer_id.exists' => __('The selected customer is invalid.'),
            'start_time.required' => __('The start time is required.'),
            'start_time.date_format' => __('Please provide a valid start time format (HH:MM:SS).'),
            'end_time.required' => __('The end time is required.'),
            'end_time.date_format' => __('Please provide a valid end time format (HH:MM:SS).'),
            'end_time.after' => __('The end time must be after the start time.'),
            'status.required' => __('The appointment status is required.'),
            'item_id.exists' => __('The selected item is invalid.'),
            'package_id.exists' => __('The selected package is invalid.'),
        ];
    }
}