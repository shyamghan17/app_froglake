<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\CreateInterviewFeedback;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateInterviewFeedbackLis
{
    public function handle(CreateInterviewFeedback $event)
    {
        if (Module_is_active('ActivityLog')) {
            $interviewfeedback = $event->interviewFeedback;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Interview Feedback';
            $activity['description'] = __('New Interview Feedback created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $interviewfeedback->created_by;
            $activity->save();
        }
    }
}
