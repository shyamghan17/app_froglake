<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\UpdateAttendance as SchoolUpdateAttendance;

class UpdateSchoolAttendanceLis
{
    public function handle(SchoolUpdateAttendance $event)
    {
        if (Module_is_active('ActivityLog')) {
            $attendance = $event->attendance;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Attendance';
            $activity['description'] = __('School Attendance updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $attendance->created_by;
            $activity->save();
        }
    }
}