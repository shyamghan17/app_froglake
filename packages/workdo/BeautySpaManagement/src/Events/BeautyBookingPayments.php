<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\BeautySpaManagement\Models\BeautyBooking;

class BeautyBookingPayments
{
    use Dispatchable;

    public function __construct(
        public BeautyBooking $booking
    ) {}
}
