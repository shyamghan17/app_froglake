<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautySubscriber;

class DestroyBeautySubscriber
{
    use Dispatchable;

    public function __construct(
        public BeautySubscriber $beautySubscriber
    ) {}
}