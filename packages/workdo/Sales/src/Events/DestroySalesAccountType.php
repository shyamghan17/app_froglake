<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesAccountType;

class DestroySalesAccountType
{
    use Dispatchable;

    public function __construct(
        public SalesAccountType $accountType
    ) {}
}