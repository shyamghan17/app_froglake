<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesOrderItem;

class DestroySalesOrderItem
{
    use Dispatchable;

    public function __construct(
        public SalesOrderItem $salesOrderItem,
    ) {}
}