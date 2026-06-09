<?php

namespace Workdo\OpticalAndEyeCareCenter\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\OpticalAndEyeCareCenter\Models\EyewearOrder;

class PostEyewearOrder
{
    use Dispatchable;

     public function __construct(
        public EyewearOrder $eyewearOrder


  ) {}
}
