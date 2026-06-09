<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\Sales\Models\SalesOrderItem;

class CreateSalesOrderItem
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public SalesOrderItem $salesOrderItem
    ) {}
}