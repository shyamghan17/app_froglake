<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesMeeting;

class DestroySalesMeeting
{
    use Dispatchable;

    public function __construct(
        public SalesMeeting $meeting
    ) {}
}