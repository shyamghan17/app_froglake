<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Bookings\Models\BookingStaff;

class DestroyBookingStaff
{
    use Dispatchable;

    public function __construct(
        public BookingStaff $staff
    ) {}
}