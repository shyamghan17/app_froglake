<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateLocationLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $location = $event->location;

            $activity = new AllActivityLog();
            $activity['module'] = 'CMMS';
            $activity['sub_module'] = 'Location';
            $activity['description'] = __('Location updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $location->created_by;
            $activity->save();
        }
    }
}