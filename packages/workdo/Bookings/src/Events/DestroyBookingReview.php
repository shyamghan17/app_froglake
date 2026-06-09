<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Bookings\Models\BookingReview;

class DestroyBookingReview
{
    use Dispatchable;

    public function __construct(
        public BookingReview $review
    ) {}
}