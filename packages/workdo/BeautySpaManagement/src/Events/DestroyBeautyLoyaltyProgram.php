<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyLoyaltyProgram;

class DestroyBeautyLoyaltyProgram
{
    use Dispatchable;

    public function __construct(
        public BeautyLoyaltyProgram $beautyloyaltyprogram
    ) {}
}