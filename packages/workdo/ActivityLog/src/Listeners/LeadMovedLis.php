<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class LeadMovedLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $lead = $event->lead;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Lead';
            $activity['description'] = __('Lead moved by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $lead->created_by;
            $activity->save();
        }
    }
}