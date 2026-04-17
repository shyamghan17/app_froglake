<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class DealAddDiscussionLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $dealDiscussion = $event->deal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Deal';
            $activity['description'] = __('New Discussion Add in deal ') . $dealDiscussion->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $dealDiscussion->created_by;
            $activity->save();
        }
    }
}