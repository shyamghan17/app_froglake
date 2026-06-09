<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointmentPayment;

class DestroyPhotoStudioAppointmentPayment
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioAppointmentPayment $payment
    ) {}
}
