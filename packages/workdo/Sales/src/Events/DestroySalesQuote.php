<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesQuote;

class DestroySalesQuote
{
    use Dispatchable;

    public function __construct(
        public SalesQuote $quote
    ) {}
}