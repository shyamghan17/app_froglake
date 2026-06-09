<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateAttendance;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateAttendanceLis
{
    public function handle(CreateAttendance $event)
    {
        if (Module_is_active('ActivityLog')) {
            $attendance = $event->attendance;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Attendance';
            $activity['description'] = __('New Attendance created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $attendance->created_by;
            $activity->save();
        }
    }
}
