<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePhotoStudioAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'mobile_no'           => 'required|string|max:20',
            'booking_start_date'  => 'required|date',
            'booking_end_date'    => 'required|date|after_or_equal:booking_start_date',
            'service_id'          => 'required|integer|exists:photo_studio_services,id',
            'price'               => 'required|numeric|min:0',
        ];
    }
}
