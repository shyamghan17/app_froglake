<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\LeaveType;

class DestroyLeaveType
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public LeaveType $leaveType
    )
    {}
}