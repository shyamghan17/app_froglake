<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesOpportunity;

class DestroySalesOpportunity
{
    use Dispatchable;

    public function __construct(
        public SalesOpportunity $opportunity
    ) {}
}