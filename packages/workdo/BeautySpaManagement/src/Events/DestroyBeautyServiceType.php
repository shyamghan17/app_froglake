<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyServiceType;

class DestroyBeautyServiceType
{
    use Dispatchable;

    public function __construct(
        public BeautyServiceType $servicetype
    ) {}
}