<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;

class DestroyRepairOrderRequest
{
    use Dispatchable;

    public function __construct(
        public RepairOrderRequest $repairOrderRequest,
    ) {}
}