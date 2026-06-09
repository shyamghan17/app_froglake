<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\TeamWorkload\Events\CreateTeamWorkloadHoliday;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateTeamWorkloadHolidayLis
{
    public function handle(CreateTeamWorkloadHoliday $event)
    {
        if (Module_is_active('ActivityLog')) {
            $holiday = $event->holiday;

            $activity = new AllActivityLog();
            $activity['module'] = 'TeamWorkload';
            $activity['sub_module'] = 'Holiday';
            $activity['description'] = __('New Holiday created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $holiday->created_by;
            $activity->save();
        }
    }
}
