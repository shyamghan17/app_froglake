<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioSubscriber;

class DestroyPhotoStudioSubscriber
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioSubscriber $subscriber
    ) {}
}