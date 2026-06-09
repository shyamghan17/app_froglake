<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\EyeCareAppoinment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class UpdateEyeCareAppoinment
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public EyeCareAppoinment $eyeCareAppoinment
    ) {}
}
