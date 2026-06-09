<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesCall;

class DestroySalesCall
{
    use Dispatchable;

    public function __construct(
        public SalesCall $salesCall
    ) {}
}