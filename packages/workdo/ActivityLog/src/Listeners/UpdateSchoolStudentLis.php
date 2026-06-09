<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\UpdateStudent as SchoolUpdateStudent;

class UpdateSchoolStudentLis
{
    public function handle(SchoolUpdateStudent $event)
    {
        if (Module_is_active('ActivityLog')) {
            $student = $event->student;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Student';
            $activity['description'] = __('School Student updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $student->created_by;
            $activity->save();
        }
    }
}