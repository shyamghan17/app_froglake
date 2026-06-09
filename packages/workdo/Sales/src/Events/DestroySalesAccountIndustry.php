<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesAccountIndustry;

class DestroySalesAccountIndustry
{
    use Dispatchable;

    public function __construct(
        public SalesAccountIndustry $accountIndustry
    ) {}
}