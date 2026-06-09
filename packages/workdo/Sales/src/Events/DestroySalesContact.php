<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesContact;

class DestroySalesContact
{
    use Dispatchable;

    public function __construct(
        public SalesContact $contact
    ) {}
}