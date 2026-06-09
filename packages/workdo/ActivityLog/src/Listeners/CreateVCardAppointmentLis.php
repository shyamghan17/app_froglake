<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Workdo\VCard\Events\CreateAppointment;

class CreateVCardAppointmentLis
{
    public function handle(CreateAppointment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $appointment = $event->appointment;

            $activity = new AllActivityLog();
            $activity['module'] = 'VCard';
            $activity['sub_module'] = 'Appointment';
            $activity['description'] = __('New VCard Appointment created by the ');
            $activity['creator_id'] = $appointment->creator_id ?? null;
            $activity['created_by'] = $appointment->created_by;
            $activity->save();
        }
    }
}
