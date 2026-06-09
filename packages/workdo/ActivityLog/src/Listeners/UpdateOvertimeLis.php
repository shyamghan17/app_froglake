<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateOverTime;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateOvertimeLis
{
    public function handle(UpdateOverTime $event)
    {
        if (Module_is_active('ActivityLog')) {
            $overtime = $event->overtime;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Overtime';
            $activity['description'] = __('Overtime updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $overtime->created_by;
            $activity->save();
        }
    }
}
