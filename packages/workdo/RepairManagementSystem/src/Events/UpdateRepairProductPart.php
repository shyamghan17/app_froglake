<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Workdo\RepairManagementSystem\Models\RepairPart;

class UpdateRepairProductPart
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public RepairPart $repairPart
    ) {}
}