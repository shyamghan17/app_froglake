<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEyeTestPrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_name' => 'required|exists:users,id',
            'test_date' => 'required|date',
            'test_results' => 'nullable',
            'prescription_details' => 'nullable',
            'prescription_expiry_date' => 'nullable|date',
            'notes' => 'nullable',
            'patient_id' => 'required|exists:eye_patients,id'
        ];
    }
}