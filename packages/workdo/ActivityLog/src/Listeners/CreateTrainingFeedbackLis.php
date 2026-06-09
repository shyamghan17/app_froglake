<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Training\Events\CreateTrainingFeedback;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateTrainingFeedbackLis
{
    public function handle(CreateTrainingFeedback $event)
    {
        if (Module_is_active('ActivityLog')) {
            $feedback = $event->feedback;

            $activity = new AllActivityLog();
            $activity['module'] = 'Training';
            $activity['sub_module'] = 'Training Feedback';
            $activity['description'] = __('New Training Feedback created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $feedback->created_by;
            $activity->save();
        }
    }
}