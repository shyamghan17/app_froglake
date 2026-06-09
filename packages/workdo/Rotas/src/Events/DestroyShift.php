<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\Shift;

class DestroyShift
{
    use Dispatchable, SerializesModels;

    public function __construct(
          public Shift $shift
    )
    {
        //
    }
}