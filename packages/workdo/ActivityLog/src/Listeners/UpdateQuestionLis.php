<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Appointment\Events\UpdateQuestion;

class UpdateQuestionLis
{
    public function handle(UpdateQuestion $event)
    {
        if (Module_is_active('ActivityLog')) {
            $question = $event->question;

            $activity = new AllActivityLog();
            $activity['module'] = 'Appointment';
            $activity['sub_module'] = 'Question';
            $activity['description'] = __('Question updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $question->created_by;
            $activity->save();
        }
    }
}
