<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\RotasAvailability;
use Illuminate\Http\Request;

class CreateAvailability
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Request $request,
        public RotasAvailability $availability
    )
    {}
}