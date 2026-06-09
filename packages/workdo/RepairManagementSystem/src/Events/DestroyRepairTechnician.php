<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\RepairManagementSystem\Models\RepairTechnician;

class DestroyRepairTechnician
{
    use Dispatchable;

    public function __construct(
        public RepairTechnician $repairTechnician,
    ) {}
}