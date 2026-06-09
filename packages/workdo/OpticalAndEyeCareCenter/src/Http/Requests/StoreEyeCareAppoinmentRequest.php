<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEyeCareAppoinmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_name' => 'required|exists:users,id',
            'appointment_datetime' => 'required',
            'status' => 'required',
            'appointment_type' => 'required',
            'notes' => 'nullable',
            'patient_id' => 'required|exists:eye_patients,id'
        ];
    }
}