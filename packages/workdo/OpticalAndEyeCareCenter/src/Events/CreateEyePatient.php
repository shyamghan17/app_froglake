<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class CreateEyePatient
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public EyePatient $eyePatient
    ) {}
}
