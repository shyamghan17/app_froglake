<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyBooking;
use Workdo\BeautySpaManagement\Models\BeautyBookingPayment;

class MarkBeautyBookingPaymentPaid
{
    use Dispatchable;

    public function __construct(
        public BeautyBookingPayment $payment,
        public BeautyBooking $booking
    )
    {}

}
