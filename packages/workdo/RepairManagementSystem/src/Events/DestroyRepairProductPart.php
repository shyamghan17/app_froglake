<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\RepairManagementSystem\Models\RepairPart;

class DestroyRepairProductPart
{
    use Dispatchable;

    public function __construct(
        public RepairPart $repairPart,
    ) {}
}