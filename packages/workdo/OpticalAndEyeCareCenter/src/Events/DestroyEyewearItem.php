<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\EyewearItem;
use Illuminate\Foundation\Events\Dispatchable;

class DestroyEyewearItem
{
    use Dispatchable;

    public function __construct(
        public EyewearItem $eyewearItem
    ) {}
}
