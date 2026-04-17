<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class DealAddCallLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $dealCall = $event->deal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Deal';
            $activity['description'] = __('New Call Created in deal ') . $dealCall->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $dealCall->created_by;
            $activity->save();
        }
    }
}