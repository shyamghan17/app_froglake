<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\Department;
use Illuminate\Http\Request;

class UpdateDepartment
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Request $request,
        public Department $department
    )
    {}
}