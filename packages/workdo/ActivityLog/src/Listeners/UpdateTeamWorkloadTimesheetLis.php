<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\TeamWorkload\Events\UpdateTeamWorkloadTimesheet;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateTeamWorkloadTimesheetLis
{
    public function handle(UpdateTeamWorkloadTimesheet $event)
    {
        if (Module_is_active('ActivityLog')) {
            $timesheet = $event->timesheet;

            $activity = new AllActivityLog();
            $activity['module'] = 'TeamWorkload';
            $activity['sub_module'] = 'Timesheet';
            $activity['description'] = __('Timesheet entry updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $timesheet->created_by;
            $activity->save();
        }
    }
}
