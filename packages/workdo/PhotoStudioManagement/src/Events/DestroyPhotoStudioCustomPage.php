<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCustomPage;

class DestroyPhotoStudioCustomPage
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioCustomPage $customPage
    ) {}
}
