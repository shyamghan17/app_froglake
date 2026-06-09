<?php

namespace Workdo\BeautySpaManagement\Events;

use Workdo\BeautySpaManagement\Models\BeautyWorking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class UpdateWorkingHours
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public BeautyWorking $workingHours
    ) {}
}