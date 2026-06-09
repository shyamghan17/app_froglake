<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Lead\Events\LeadAddDiscussion;

class LeadAddDiscussionLis
{
    public function handle(LeadAddDiscussion $event)
    {
        if (Module_is_active('ActivityLog')) {
            $leadDiscussion = $event->lead;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Lead';
            $activity['description'] = __('New Lead Discussion added by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $leadDiscussion->created_by;
            $activity->save();
        }
    }
}
