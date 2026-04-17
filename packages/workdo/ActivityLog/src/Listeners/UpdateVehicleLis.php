<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateVehicleLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $vehicle = $event->vehicle;

            $activity = new AllActivityLog();
            $activity['module'] = 'GarageManagement';
            $activity['sub_module'] = 'Vehicle';
            $activity['description'] = __('Vehicle updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $vehicle->created_by;
            $activity->save();
        }
    }
}