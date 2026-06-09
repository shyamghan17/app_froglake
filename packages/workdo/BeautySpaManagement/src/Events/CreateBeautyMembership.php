<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\BeautySpaManagement\Models\BeautyMembership;

class CreateBeautyMembership
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public BeautyMembership $beautymembership
    ) {}
}