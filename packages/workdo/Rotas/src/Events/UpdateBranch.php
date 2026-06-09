<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\Branch;
use Illuminate\Http\Request;

class UpdateBranch
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Request $request,
        public Branch $branch
    )
    {}
}