<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesCase;

class DestroySalesCase
{
    use Dispatchable;

    public function __construct(
        public SalesCase $salesCase,
    ) {}
}