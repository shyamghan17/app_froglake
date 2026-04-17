<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdatePreventiveMaintenanceLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $preventiveMaintenance = $event->preventiveMaintenance;

            $activity = new AllActivityLog();
            $activity['module'] = 'CMMS';
            $activity['sub_module'] = 'Preventive Maintenance';
            $activity['description'] = __('Preventive Maintenance updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $preventiveMaintenance->created_by;
            $activity->save();
        }
    }
}