<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\Department;
use Illuminate\Http\Request;

class DestroyDepartment
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Department $department
    )
    {}
}