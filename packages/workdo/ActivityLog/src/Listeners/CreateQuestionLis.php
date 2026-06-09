<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Appointment\Events\CreateQuestion;

class CreateQuestionLis
{
    public function handle(CreateQuestion $event)
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
