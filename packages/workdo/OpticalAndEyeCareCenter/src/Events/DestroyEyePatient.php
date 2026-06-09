<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;
use Illuminate\Foundation\Events\Dispatchable;

class DestroyEyePatient
{
    use Dispatchable;

    public function __construct(
        public EyePatient $eyePatient
    ) {}
}
