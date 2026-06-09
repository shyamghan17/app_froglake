<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Bookings\Models\BookingPackage;

class DestroyBookingPackage
{
    use Dispatchable;

    public function __construct(
        public BookingPackage $package
    ) {}
}