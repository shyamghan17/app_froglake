<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesCaseType;

class DestroySalesCaseType
{
    use Dispatchable;

    public function __construct(
        public SalesCaseType $salesCaseType
    ) {}
}