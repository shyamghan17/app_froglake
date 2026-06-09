<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyBooking;

class DestroyBeautyBooking
{
    use Dispatchable;

    public function __construct(
        public BeautyBooking $beautybooking
    ) {}
}