<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\EyeCareAppoinment;
use Illuminate\Foundation\Events\Dispatchable;

class DestroyEyeCareAppoinment
{
    use Dispatchable;

    public function __construct(
        public EyeCareAppoinment $eyeCareAppoinment
    ) {}
}
