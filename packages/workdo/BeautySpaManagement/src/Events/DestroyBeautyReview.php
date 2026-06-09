<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyReview;

class DestroyBeautyReview
{
    use Dispatchable;

    public function __construct(
    public BeautyReview $beautyreview,
) {}
}