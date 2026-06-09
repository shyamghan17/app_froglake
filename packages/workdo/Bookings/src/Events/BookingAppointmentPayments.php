<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Bookings\Models\BookingAppointment;

class BookingAppointmentPayments
{
    use Dispatchable;

    public function __construct(
        public BookingAppointment $appointment
    ) {}
}
