<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\BeautySpaManagement\Models\BeautyServiceOffer;

class UpdateBeautyServiceOffer
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public BeautyServiceOffer $beautyserviceoffer
    ) {}
}