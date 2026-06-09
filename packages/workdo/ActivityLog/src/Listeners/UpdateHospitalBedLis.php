<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\HospitalManagement\Events\UpdateHospitalBed;

class UpdateHospitalBedLis
{
    public function handle(UpdateHospitalBed $event)
    {
        if (Module_is_active('ActivityLog')) {
            $hospitalBed = $event->hospitalbed;

            $activity = new AllActivityLog();
            $activity['module'] = 'HospitalManagement';
            $activity['sub_module'] = 'Bed';
            $activity['description'] = __('Hospital Bed updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $hospitalBed->created_by;
            $activity->save();
        }
    }
}
