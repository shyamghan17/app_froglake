<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\UpdateInterviewFeedback;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateInterviewFeedbackLis
{
    public function handle(UpdateInterviewFeedback $event)
    {
        if (Module_is_active('ActivityLog')) {
            $interviewfeedback = $event->interviewFeedback;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Interview Feedback';
            $activity['description'] = __('Interview Feedback updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $interviewfeedback->created_by;
            $activity->save();
        }
    }
}
