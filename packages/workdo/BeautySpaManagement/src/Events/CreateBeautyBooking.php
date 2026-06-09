<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\BeautySpaManagement\Models\BeautyBooking;

class CreateBeautyBooking
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public BeautyBooking $beautybooking
    ) {}
}