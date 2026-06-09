<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\CallHub\Events\CreateCallHubCallList;

class CreateCallHubCallListLis
{
    public function handle(CreateCallHubCallList $event)
    {
        if (Module_is_active('ActivityLog')) {
            $callList = $event->callhubcalllist;

            $activity = new AllActivityLog();
            $activity['module'] = 'CallHub';
            $activity['sub_module'] = 'Call List';
            $activity['description'] = __('New Call List created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $callList->created_by;
            $activity->save();
        }
    }
}
