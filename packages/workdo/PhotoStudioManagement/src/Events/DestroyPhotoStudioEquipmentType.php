<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentType;

class DestroyPhotoStudioEquipmentType
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioEquipmentType $photoStudioEquipmentType
    ) {}
}
