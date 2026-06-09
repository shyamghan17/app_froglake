<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;

class UpdateRepairOrderRequest
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public RepairOrderRequest $repairOrderRequest
    ) {}
}