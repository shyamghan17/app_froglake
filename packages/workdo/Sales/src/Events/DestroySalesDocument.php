<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesDocument;

class DestroySalesDocument
{
    use Dispatchable;

    public function __construct(
        public SalesDocument $salesDocument
    ) {}
}