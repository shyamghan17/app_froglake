<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesQuoteItem;

class DestroySalesQuoteItem
{
    use Dispatchable;

    public function __construct(public SalesQuoteItem $quoteItem)
    {
    }
}