<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\OpticalDoctor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class UpdateOpticalDoctor
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public OpticalDoctor $opticalDoctor
    ) {}
}
