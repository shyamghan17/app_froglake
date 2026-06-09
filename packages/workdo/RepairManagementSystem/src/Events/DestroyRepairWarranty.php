<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\RepairManagementSystem\Models\RepairWarranty;

class DestroyRepairWarranty
{
    use Dispatchable;

    public function __construct(
        public RepairWarranty $repairWarranty,
    ) {}
}