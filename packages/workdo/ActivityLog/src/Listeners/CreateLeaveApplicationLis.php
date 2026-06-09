<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateLeaveApplication;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateLeaveApplicationLis
{
    public function handle(CreateLeaveApplication $event)
    {
        if (Module_is_active('ActivityLog')) {
            $leaveApplication = $event->leaveapplication;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Leave Application';
            $activity['description'] = __('New Leave Application created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $leaveApplication->created_by;
            $activity->save();
        }
    }
}
