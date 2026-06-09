<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\LMS\Events\CreateCourse;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateLMSCourseLis
{
    public function handle(CreateCourse $event)
    {
        if (Module_is_active('ActivityLog')) {
            $course = $event->course;

            $activity = new AllActivityLog();
            $activity['module'] = 'LMS';
            $activity['sub_module'] = 'Course';
            $activity['description'] = __('New LMS Course created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $course->created_by;
            $activity->save();
        }
    }
}