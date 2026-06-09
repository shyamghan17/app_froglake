<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointment;

class UpdatePhotoStudioAppointment
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public PhotoStudioAppointment $appointment
    ) {}
}
