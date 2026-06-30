<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Requests;

class UpdateEyeTestPrescriptionRequest extends EyeTestPrescriptionRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit-eye-test-prescriptions') ?? false;
    }
}
