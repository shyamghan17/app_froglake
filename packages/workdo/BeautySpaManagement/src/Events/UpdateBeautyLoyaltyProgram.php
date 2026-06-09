<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\BeautySpaManagement\Models\BeautyLoyaltyProgram;

class UpdateBeautyLoyaltyProgram
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public BeautyLoyaltyProgram $beautyloyaltyprogram
    ) {}
}