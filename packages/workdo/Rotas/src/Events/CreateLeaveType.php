<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\LeaveType;
use Illuminate\Http\Request;

class CreateLeaveType
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Request $request,
        public LeaveType $leaveType
    )
    {}
}