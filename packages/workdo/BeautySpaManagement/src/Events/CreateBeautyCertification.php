<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\BeautySpaManagement\Models\BeautyCertification;

class CreateBeautyCertification
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public BeautyCertification $certification
    ) {}
}