<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\EyeTestPrescription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class UpdateEyeTestPrescription
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public EyeTestPrescription $eyeTestPrescription
    ) {}
}
