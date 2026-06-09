<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\CreateInterview;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateInterviewLis
{
    public function handle(CreateInterview $event)
    {
        if (Module_is_active('ActivityLog')) {
            $interview = $event->interview;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Interview';
            $activity['description'] = __('New Interview scheduled by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $interview->created_by;
            $activity->save();
        }
    }
}
