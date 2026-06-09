<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\TimeTracker\Events\CreateTimeTracker;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateTimeTrackerLis
{
    public function handle(CreateTimeTracker $event)
    {
        if (Module_is_active('ActivityLog')) {
            $timeTracker = $event->timeTracker;

            $activity = new AllActivityLog();
            $activity['module'] = 'TimeTracker';
            $activity['sub_module'] = 'Time Tracker';
            $activity['description'] = __('New Time Tracker started by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $timeTracker->created_by;
            $activity->save();
        }
    }
}
