<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\Rota;
use Illuminate\Http\Request;

class DestroyRota
{
    use Dispatchable, SerializesModels;

    public function __construct(
          public Rota $rota
    )
    {
        //
    }
}