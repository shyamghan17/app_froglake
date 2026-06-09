<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class CreateEyewearOrder
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public EyewearOrder $eyewearOrder
    ) {}
}
