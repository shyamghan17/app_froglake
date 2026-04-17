<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class LeadAddEmailLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $leadEmail = $event->lead;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Lead';
            $activity['description'] = __('New Lead Email added by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $leadEmail->created_by;
            $activity->save();
        }
    }
}