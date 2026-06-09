<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\Sales\Models\SalesAccount;

class UpdateSalesAccount
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public SalesAccount $account
    ) {}
}