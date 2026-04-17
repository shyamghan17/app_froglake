<?php

namespace Workdo\ActivityLog\Listeners;

use Illuminate\Support\Facades\Auth;
use Workdo\ActivityLog\Models\AllActivityLog;

class CreateLeadLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $lead = $event->lead;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Lead';
            $activity['description'] = __('New Lead created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $lead->created_by;
            $activity->save();
        }
    }
}