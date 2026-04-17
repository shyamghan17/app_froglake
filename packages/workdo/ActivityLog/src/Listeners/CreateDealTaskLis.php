<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateDealTaskLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $dealTask = $event->dealTask;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Deal Task';
            $activity['description'] = __('New Task Created in deal ') . $dealTask->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $dealTask->created_by;
            $activity->save();
        }
    }
}