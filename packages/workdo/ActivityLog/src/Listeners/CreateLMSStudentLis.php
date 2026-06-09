<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\LMS\Events\CreateLMSStudent;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateLMSStudentLis
{
    public function handle(CreateLMSStudent $event)
    {
        if (Module_is_active('ActivityLog')) {
            $student = $event->student;

            $activity = new AllActivityLog();
            $activity['module'] = 'LMS';
            $activity['sub_module'] = 'Student';
            $activity['description'] = __('New LMS Student registered by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $student->created_by ?? Auth::user()->id;
            $activity->save();
        }
    }
}