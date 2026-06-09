<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioGalleryType;

class DestroyPhotoStudioGalleryType
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioGalleryType $photoStudioGalleryType
    ) {}
}
