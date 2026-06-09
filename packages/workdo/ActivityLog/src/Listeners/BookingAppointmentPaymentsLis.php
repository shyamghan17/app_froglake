<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Workdo\Bookings\Events\BookingAppointmentPayments;

class BookingAppointmentPaymentsLis
{
    public function handle(BookingAppointmentPayments $event)
    {
        $appointment = $event->appointment;
        if (Module_is_active('ActivityLog',$appointment->created_by)) {

            $activity = new AllActivityLog();
            $activity['module'] = 'Bookings';
            $activity['sub_module'] = 'Appointment';
            $activity['description'] = __('Booking Appointment Payment done by the ');
            $activity['creator_id'] = $appointment->creator_id;
            $activity['created_by'] = $appointment->created_by;
            $activity->save();
        }
    }
}
