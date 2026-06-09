<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Bookings\Events\UpdateBookingAppointment;

class UpdateBookingAppointmentLis
{
    public function handle(UpdateBookingAppointment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $appointment = $event->appointment;

            $activity = new AllActivityLog();
            $activity['module'] = 'Bookings';
            $activity['sub_module'] = 'Appointment';
            $activity['description'] = __('Booking Appointment updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $appointment->created_by;
            $activity->save();
        }
    }
}
