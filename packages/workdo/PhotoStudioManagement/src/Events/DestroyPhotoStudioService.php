<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioService;

class DestroyPhotoStudioService
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioService $photoStudioService
    ) {}
}
