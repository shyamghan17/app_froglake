<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\Designation;
use Illuminate\Http\Request;

class UpdateDesignation
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Request $request,
        public Designation $designation
    )
    {}
}