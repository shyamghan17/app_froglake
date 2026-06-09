<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\Bookings\Models\BookingExtraService;

class UpdateBookingExtraService
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public BookingExtraService $extraService
    ) {}
}