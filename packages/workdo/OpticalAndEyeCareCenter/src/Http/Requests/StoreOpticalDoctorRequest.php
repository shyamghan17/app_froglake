<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpticalDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'                    => 'required|exists:users,id',
            'license_number'             => 'required|string|max:255',
            'gender'                     => 'required',
            'years_of_experience'        => 'required|integer|min:0',
            'consultation_fee'           => 'nullable|numeric|min:0',
            'qualifications'             => 'nullable',
            'status'                     => 'required',
        ];
    }
}
