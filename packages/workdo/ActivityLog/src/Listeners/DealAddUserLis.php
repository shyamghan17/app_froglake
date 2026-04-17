<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class DealAddUserLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $dealUser = $event->deal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Deal';
            $activity['description'] = __('New User Add in deal ') . $dealUser->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $dealUser->created_by;
            $activity->save();
        }
    }
}