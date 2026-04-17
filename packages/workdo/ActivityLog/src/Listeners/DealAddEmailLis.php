<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class DealAddEmailLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $dealEmail = $event->deal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Deal';
            $activity['description'] = __('New Email Add in deal ') . $dealEmail->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $dealEmail->created_by;
            $activity->save();
        }
    }
}