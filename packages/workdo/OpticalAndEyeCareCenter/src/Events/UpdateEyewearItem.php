<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Workdo\OpticalAndEyeCareCenter\Models\EyewearItem;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class UpdateEyewearItem
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public EyewearItem $eyewearItem
    ) {}
}
