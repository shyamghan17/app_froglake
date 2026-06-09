<?php

namespace Workdo\BeautySpaManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\BeautySpaManagement\Models\BeautyContact;

class DestroyBeautyContact
{
    use Dispatchable;

    public function __construct(
    public BeautyContact $beautycontact,
) {}
}