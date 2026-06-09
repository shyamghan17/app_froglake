<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\RotasAvailability;

class DestroyAvailability
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public RotasAvailability $availability
    )
    {}
}