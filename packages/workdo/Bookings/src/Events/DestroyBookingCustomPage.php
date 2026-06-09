<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Bookings\Models\BookingCustomPage;

class DestroyBookingCustomPage
{
    use Dispatchable;

    public function __construct(
        public BookingCustomPage $page
    ) {}
}