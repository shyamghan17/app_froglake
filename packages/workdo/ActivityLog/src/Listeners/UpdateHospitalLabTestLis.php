<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateHospitalLabTestLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $hospitalLabTest = $event->hospitallabtest;

            $activity = new AllActivityLog();
            $activity['module'] = 'HospitalManagement';
            $activity['sub_module'] = 'LabTest';
            $activity['description'] = __('Hospital Lab Test updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $hospitalLabTest->created_by;
            $activity->save();
        }
    }
}