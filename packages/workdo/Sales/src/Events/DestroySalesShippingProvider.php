<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesShippingProvider;

class DestroySalesShippingProvider
{
    use Dispatchable;

    public function __construct(
        public SalesShippingProvider $shippingProvider
    ) {}
}