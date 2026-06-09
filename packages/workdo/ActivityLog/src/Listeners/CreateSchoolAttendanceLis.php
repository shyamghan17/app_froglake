<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\CreateAttendance as SchoolCreateAttendance;

class CreateSchoolAttendanceLis
{
    public function handle(SchoolCreateAttendance $event)
    {
        if (Module_is_active('ActivityLog')) {
            $attendance = $event->attendance;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Attendance';
            $activity['description'] = __('New School Attendance recorded by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $attendance->created_by;
            $activity->save();
        }
    }
}