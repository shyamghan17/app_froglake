<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCameraKit;

class DestroyPhotoStudioCameraKit
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioCameraKit $photoStudioCameraKit
    ) {}
}
