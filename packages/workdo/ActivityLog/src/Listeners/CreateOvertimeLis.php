<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateOverTime;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateOvertimeLis
{
    public function handle(CreateOverTime $event)
    {
        if (Module_is_active('ActivityLog')) {
            $overtime = $event->overtime;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Overtime';
            $activity['description'] = __('New Overtime created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $overtime->created_by;
            $activity->save();
        }
    }
}
