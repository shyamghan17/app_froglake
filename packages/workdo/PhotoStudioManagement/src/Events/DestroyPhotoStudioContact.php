<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioContact;

class DestroyPhotoStudioContact
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioContact $contact
    ) {}
}