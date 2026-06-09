<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\UpdateHomework;

class UpdateSchoolHomeworkLis
{
    public function handle(UpdateHomework $event)
    {
        if (Module_is_active('ActivityLog')) {
            $homework = $event->homework;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Homework';
            $activity['description'] = __('School Homework updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $homework->created_by;
            $activity->save();
        }
    }
}