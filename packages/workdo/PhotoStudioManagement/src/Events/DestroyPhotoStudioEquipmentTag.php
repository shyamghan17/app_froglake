<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentTag;

class DestroyPhotoStudioEquipmentTag
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioEquipmentTag $photoStudioEquipmentTag
    ) {}
}
