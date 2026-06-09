<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\Sales\Models\SalesContact;

class UpdateSalesContact
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public SalesContact $contact
    ) {}
}