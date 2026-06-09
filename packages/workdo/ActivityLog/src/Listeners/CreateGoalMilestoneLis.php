<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Goal\Events\CreateGoalMilestone;

class CreateGoalMilestoneLis
{
    public function handle(CreateGoalMilestone $event)
    {
        if (Module_is_active('ActivityLog')) {
            $milestone = $event->milestone;

            $activity = new AllActivityLog();
            $activity['module'] = 'Goal';
            $activity['sub_module'] = 'Milestone';
            $activity['description'] = __('New Goal Milestone created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $milestone->created_by;
            $activity->save();
        }
    }
}