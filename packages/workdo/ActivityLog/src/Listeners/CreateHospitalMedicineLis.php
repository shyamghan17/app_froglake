<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateHospitalMedicineLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $hospitalMedicine = $event->hospitalMedicine;

            $activity = new AllActivityLog();
            $activity['module'] = 'HospitalManagement';
            $activity['sub_module'] = 'Medicine';
            $activity['description'] = __('Hospital Medicine created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $hospitalMedicine->created_by;
            $activity->save();
        }
    }
}