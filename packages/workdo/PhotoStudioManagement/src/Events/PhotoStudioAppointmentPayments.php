<?php

namespace Workdo\PhotoStudioManagement\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointment;
use Workdo\PhotoStudioManagement\Models\PhotoStudioAppointmentPayment;

class PhotoStudioAppointmentPayments
{
    use Dispatchable;

    public function __construct(
        public PhotoStudioAppointment $appointment,
        public PhotoStudioAppointmentPayment $payment
    ) {}
}
