<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyService;

class DestroyBeautyService
{
    use Dispatchable;

    public function __construct(
        public BeautyService $service
    ) {}
}