<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\Employee;
use Illuminate\Http\Request;

class UpdateEmployee
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Request $request,
        public Employee $employee
    )
    {}
}