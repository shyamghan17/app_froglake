<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateLocationLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $location = $event->location;

            $activity = new AllActivityLog();
            $activity['module'] = 'CMMS';
            $activity['sub_module'] = 'Location';
            $activity['description'] = __('New Location created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $location->created_by;
            $activity->save();
        }
    }
}