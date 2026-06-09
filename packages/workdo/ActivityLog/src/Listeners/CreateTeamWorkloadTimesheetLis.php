<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\TeamWorkload\Events\CreateTeamWorkloadTimesheet;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateTeamWorkloadTimesheetLis
{
    public function handle(CreateTeamWorkloadTimesheet $event)
    {
        if (Module_is_active('ActivityLog')) {
            $timesheet = $event->timesheet;

            $activity = new AllActivityLog();
            $activity['module'] = 'TeamWorkload';
            $activity['sub_module'] = 'Timesheet';
            $activity['description'] = __('New Timesheet entry created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $timesheet->created_by;
            $activity->save();
        }
    }
}
