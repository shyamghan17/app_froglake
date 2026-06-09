<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\GarageManagement\Events\CreateVehicle;

class CreateVehicleLis
{
    public function handle(CreateVehicle $event)
    {
        if (Module_is_active('ActivityLog')) {
            $vehicle = $event->vehicle;

            $activity = new AllActivityLog();
            $activity['module'] = 'GarageManagement';
            $activity['sub_module'] = 'Vehicle';
            $activity['description'] = __('Vehicle created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $vehicle->created_by;
            $activity->save();
        }
    }
}
