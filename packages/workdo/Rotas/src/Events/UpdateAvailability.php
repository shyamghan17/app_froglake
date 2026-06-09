<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Workdo\Rotas\Models\RotasAvailability;

class UpdateAvailability
{
    use Dispatchable, SerializesModels;

   public function __construct(
        public Request $request,
        public RotasAvailability $availability
    ) {

    }
}