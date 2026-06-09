<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Appointment\Events\AppointmentStatus;

class AppointmentStatusLis
{
    public function handle(AppointmentStatus $event)
    {
        if (Module_is_active('ActivityLog')) {
            $appointment = $event->schedule;

            $activity = new AllActivityLog();
            $activity['module'] = 'Appointment';
            $activity['sub_module'] = 'Appointment';
            $activity['description'] = __('Appointment status changed by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $appointment->created_by;
            $activity->save();
        }
    }
}
