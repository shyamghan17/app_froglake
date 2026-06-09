<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointment;

class DestroyPhotoStudioAppointment
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioAppointment $appointment
    ) {}
}
