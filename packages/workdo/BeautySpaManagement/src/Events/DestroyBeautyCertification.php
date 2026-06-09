<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyCertification;

class DestroyBeautyCertification
{
    use Dispatchable;

    public function __construct(
        public BeautyCertification $certification
    ) {}
}