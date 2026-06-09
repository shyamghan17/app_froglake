<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\RepairManagementSystem\Models\RepairInvoice;

class DestroyRepairInvoice
{
    use Dispatchable;

    public function __construct(
        public RepairInvoice $repairInvoice,
    ) {}
}