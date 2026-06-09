<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesOrder;

class DestroySalesOrder
{
    use Dispatchable;

    public function __construct(
        public SalesOrder $salesOrder,
    ) {}
}