<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\UpdateInterview;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateInterviewLis
{
    public function handle(UpdateInterview $event)
    {
        if (Module_is_active('ActivityLog')) {
            $interview = $event->interview;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Interview';
            $activity['description'] = __('Interview updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $interview->created_by;
            $activity->save();
        }
    }
}
