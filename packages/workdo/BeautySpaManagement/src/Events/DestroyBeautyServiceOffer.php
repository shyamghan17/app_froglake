<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyBooking;
use Workdo\BeautySpaManagement\Models\BeautyServiceOffer;

class DestroyBeautyServiceOffer
{
    use Dispatchable;

    public function __construct(
        public BeautyServiceOffer $beautyserviceoffer
    ) {}
}