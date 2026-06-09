<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Bookings\Models\BookingCustomer;

class DestroyBookingCustomer
{
    use Dispatchable;

    public function __construct(
        public BookingCustomer $customer
    ) {}
}