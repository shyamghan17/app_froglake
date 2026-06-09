<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\CreateStudent as SchoolCreateStudent;

class CreateSchoolStudentLis
{
    public function handle(SchoolCreateStudent $event)
    {
        if (Module_is_active('ActivityLog')) {
            $student = $event->student;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Student';
            $activity['description'] = __('New School Student created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $student->created_by;
            $activity->save();
        }
    }
}