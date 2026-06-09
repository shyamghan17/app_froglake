<?php

namespace Workdo\Portfolio\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\Portfolio\Models\PortfolioCategory;

class UpdatePortfolioCategory
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public PortfolioCategory $portfolioCategory
    ) {
    }
}
