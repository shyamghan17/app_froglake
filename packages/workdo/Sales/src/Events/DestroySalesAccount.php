<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesAccount;

class DestroySalesAccount
{
    use Dispatchable;

    public function __construct(
        public SalesAccount $account
    ) {}
}