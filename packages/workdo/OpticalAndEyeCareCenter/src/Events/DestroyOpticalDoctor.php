<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;
use Illuminate\Foundation\Events\Dispatchable;

class DestroyOpticalDoctor
{
    use Dispatchable;

    public function __construct(
        public OpticalDoctor $opticalDoctor
    ) {}
}
