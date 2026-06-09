<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyMembership;

class DestroyBeautyMembership
{
    use Dispatchable;

    public function __construct(
        public BeautyMembership $beautymembership
    ) {}
}