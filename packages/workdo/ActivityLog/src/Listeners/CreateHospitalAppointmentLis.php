<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateHospitalAppointmentLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $hospitalAppointment = $event->hospitalappointment;

            $activity = new AllActivityLog();
            $activity['module'] = 'HospitalManagement';
            $activity['sub_module'] = 'Appointment';
            $activity['description'] = __('Hospital Appointment created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $hospitalAppointment->created_by;
            $activity->save();
        }
    }
}