<?php

namespace Workdo\OpticalAndEyeCareCenter\Http\Requests;

class StoreEyeTestPrescriptionRequest extends EyeTestPrescriptionRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create-eye-test-prescriptions') ?? false;
    }
}
