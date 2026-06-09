<?php

namespace Workdo\Sales\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Sales\Models\SalesOpportunityStage;

class DestroySalesOpportunityStage
{
    use Dispatchable;

    public function __construct(
        public SalesOpportunityStage $opportunityStage
    ) {}
}