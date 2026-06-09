<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyGiftCard;

class DestroyBeautyGiftCard
{
    use Dispatchable;

    public function __construct(
        public BeautyGiftCard $giftcard
    ) {}
}