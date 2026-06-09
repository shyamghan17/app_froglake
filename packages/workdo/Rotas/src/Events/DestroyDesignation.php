<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\Designation;
use Illuminate\Http\Request;

class DestroyDesignation
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Designation $designation
    )
    {}
}