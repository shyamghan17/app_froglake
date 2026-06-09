<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:beauty_bookings,id',
            'stage_id' => 'required|in:0,1,2,3',
            'name' => 'nullable|string',
            'service' => 'nullable|string',
            'number' => 'nullable|string',
            'gender' => 'nullable|string',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'price' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|string',
        ];
    }
}