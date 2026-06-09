<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Bookings\Models\BookingContact;

class DestroyBookingContact
{
    use Dispatchable;

    public function __construct(
        public BookingContact $appointment
    ) {}
}