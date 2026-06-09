<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\EyeTestPrescription;
use Illuminate\Foundation\Events\Dispatchable;

class DestroyEyeTestPrescription
{
    use Dispatchable;

    public function __construct(
        public EyeTestPrescription $eyeTestPrescription
    ) {}
}
