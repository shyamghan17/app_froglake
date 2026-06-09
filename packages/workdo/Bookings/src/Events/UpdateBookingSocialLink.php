<?php

namespace Workdo\Bookings\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\Bookings\Models\BookingSocialLink;

class UpdateBookingSocialLink
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public BookingSocialLink $socialLink
    ) {}
}