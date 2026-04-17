<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateFixEquipmentMaintenanceLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $fixEquipmentMaintenance = $event->fixEquipmentMaintenance;

            $activity = new AllActivityLog();
            $activity['module'] = 'FixEquipment';
            $activity['sub_module'] = 'Maintenance';
            $activity['description'] = __('Fix Equipment Maintenance updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $fixEquipmentMaintenance->created_by;
            $activity->save();
        }
    }
}