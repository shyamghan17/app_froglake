<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyTraining;

class DestroyBeautyTraining
{
    use Dispatchable;

    public function __construct(
        public BeautyTraining $training
    ) {}
}