<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Appointment\Events\UpdateAppointment;

class UpdateAppointmentLis
{
    public function handle(UpdateAppointment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $appointment = $event->appointment;

            $activity = new AllActivityLog();
            $activity['module'] = 'Appointment';
            $activity['sub_module'] = 'Appointment';
            $activity['description'] = __('Appointment updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $appointment->created_by;
            $activity->save();
        }
    }
}
