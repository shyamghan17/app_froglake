<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateAttendance;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateAttendanceLis
{
    public function handle(UpdateAttendance $event)
    {
        if (Module_is_active('ActivityLog')) {
            $attendance = $event->attendance;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Attendance';
            $activity['description'] = __('Attendance updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $attendance->created_by;
            $activity->save();
        }
    }
}
