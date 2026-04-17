<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class LeadAddUserLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $leadUser = $event->lead;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Lead';
            $activity['description'] = __('New User Add in lead ') . $leadUser->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $leadUser->created_by;
            $activity->save();
        }
    }
}