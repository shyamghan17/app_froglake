<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\Sales\Models\SalesQuoteItem;

class CreateSalesQuoteItem
{
    use Dispatchable;

    public function __construct(public SalesQuoteItem $quoteItem, public Request $request)
    {
    }
}