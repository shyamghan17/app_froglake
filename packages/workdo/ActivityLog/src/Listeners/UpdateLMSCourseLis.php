<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\LMS\Events\UpdateCourse;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateLMSCourseLis
{
    public function handle(UpdateCourse $event)
    {
        if (Module_is_active('ActivityLog')) {
            $course = $event->course;

            $activity = new AllActivityLog();
            $activity['module'] = 'LMS';
            $activity['sub_module'] = 'Course';
            $activity['description'] = __('LMS Course updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $course->created_by;
            $activity->save();
        }
    }
}