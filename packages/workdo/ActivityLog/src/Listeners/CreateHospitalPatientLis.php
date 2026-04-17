<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateHospitalPatientLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $hospitalPatient = $event->hospitalpatient;

            $activity = new AllActivityLog();
            $activity['module'] = 'HospitalManagement';
            $activity['sub_module'] = 'Patient';
            $activity['description'] = __('Hospital Patient created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $hospitalPatient->created_by;
            $activity->save();
        }
    }
}