<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\PhotoStudioManagement\Models\PhotoStudioServiceCategory;

class CreatePhotoStudioServiceCategory
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public PhotoStudioServiceCategory $photoStudioServiceCategory
    ) {}
}
