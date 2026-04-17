<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateHospitalDoctorLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $hospitalDoctor = $event->hospitaldoctor;

            $activity = new AllActivityLog();
            $activity['module'] = 'HospitalManagement';
            $activity['sub_module'] = 'Doctor';
            $activity['description'] = __('Hospital Doctor updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $hospitalDoctor->created_by;
            $activity->save();
        }
    }
}