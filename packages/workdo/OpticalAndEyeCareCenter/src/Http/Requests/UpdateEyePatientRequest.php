<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEyePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_name' => 'required|max:255',
            'dob' => 'required|date',
            'gender' => 'required',
            'contact_no' => 'required|string|max:255',
            'address' => 'nullable',
            'medical_history' => 'nullable',
            'previous_prescriptions' => 'nullable',
            'preferred_doctor' => 'required|exists:users,id'
        ];
    }
}