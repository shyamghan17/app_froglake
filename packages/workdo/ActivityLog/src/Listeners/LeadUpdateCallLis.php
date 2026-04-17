<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class LeadUpdateCallLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $leadCall = $event->leadCall;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Lead';
            $activity['description'] = __('Call Updated in lead ') . $leadCall->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $leadCall->created_by;
            $activity->save();
        }
    }
}