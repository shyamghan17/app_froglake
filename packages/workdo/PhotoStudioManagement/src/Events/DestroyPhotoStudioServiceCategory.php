<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioServiceCategory;

class DestroyPhotoStudioServiceCategory
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioServiceCategory $photoStudioServiceCategory
    ) {}
}
