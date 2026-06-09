<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\PhotoStudioManagement\Models\PhotoStudioEquipmentType;

class UpdatePhotoStudioEquipmentType
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public PhotoStudioEquipmentType $photoStudioEquipmentType
    ) {}
}
