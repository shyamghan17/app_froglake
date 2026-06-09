<?php

namespace Workdo\PhotoStudioManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhotoStudioAppointmentPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'required|exists:photo_studio_appointments,id',
            'payment_date'   => 'required|date',
            'description'    => 'nullable|string',
        ];
    }
}
