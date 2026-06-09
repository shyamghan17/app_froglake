<?php

namespace Workdo\Portfolio\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Portfolio\Models\Portfolio;

class DestroyPortfolio
{
    use Dispatchable;

    public function __construct(
        public Portfolio $portfolio
    ) {
    }
}
