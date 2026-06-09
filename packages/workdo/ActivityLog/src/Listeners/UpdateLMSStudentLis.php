<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\LMS\Events\UpdateStudent;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateLMSStudentLis
{
    public function handle(UpdateStudent $event)
    {
        if (Module_is_active('ActivityLog')) {
            $student = $event->student;

            $activity = new AllActivityLog();
            $activity['module'] = 'LMS';
            $activity['sub_module'] = 'Student';
            $activity['description'] = __('LMS Student updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $student->created_by ?? Auth::user()->id;
            $activity->save();
        }
    }
}