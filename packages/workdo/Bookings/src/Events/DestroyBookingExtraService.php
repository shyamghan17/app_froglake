<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Bookings\Models\BookingExtraService;

class DestroyBookingExtraService
{
    use Dispatchable;

    public function __construct(
        public BookingExtraService $extraService
    ) {}
}