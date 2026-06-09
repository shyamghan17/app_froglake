<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Bookings\Models\BookingSocialLink;

class DestroyBookingSocialLink
{
    use Dispatchable;

    public function __construct(
        public BookingSocialLink $socialLink
    ) {}
}