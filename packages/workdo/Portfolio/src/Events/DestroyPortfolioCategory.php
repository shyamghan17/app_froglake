<?php

namespace Workdo\Portfolio\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\Portfolio\Models\PortfolioCategory;

class DestroyPortfolioCategory
{
    use Dispatchable;

    public function __construct(
        public PortfolioCategory $portfolioCategory
    ) {
    }
}
