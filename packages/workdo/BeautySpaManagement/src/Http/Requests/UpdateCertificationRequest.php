<?php

namespace Workdo\BeautySpaManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCertificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_name' => 'required|max:100',
            'certificate_name' => 'required|max:150',
            'issued_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issued_date',
            'training_id' => 'required|exists:beauty_trainings,id'
        ];
    }
}