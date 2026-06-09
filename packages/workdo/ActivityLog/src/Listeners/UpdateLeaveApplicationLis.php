<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateLeaveApplication;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateLeaveApplicationLis
{
    public function handle(UpdateLeaveApplication $event)
    {
        if (Module_is_active('ActivityLog')) {
            $leaveApplication = $event->leaveapplication;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Leave Application';
            $activity['description'] = __('Leave Application updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $leaveApplication->created_by;
            $activity->save();
        }
    }
}
