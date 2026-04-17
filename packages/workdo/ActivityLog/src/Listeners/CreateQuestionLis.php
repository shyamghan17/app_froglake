<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class   CreateQuestionLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $question = $event->question;

            $activity = new AllActivityLog();
            $activity['module'] = 'Appointment';
            $activity['sub_module'] = 'Question';
            $activity['description'] = __('New Question created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $question->created_by;
            $activity->save();
        }
    }
}