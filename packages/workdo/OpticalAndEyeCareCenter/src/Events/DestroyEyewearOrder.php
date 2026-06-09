<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrder;
use Illuminate\Foundation\Events\Dispatchable;

class DestroyEyewearOrder
{
    use Dispatchable;

    public function __construct(
        public EyewearOrder $eyewearOrder
    ) {}
}
