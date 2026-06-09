<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Goal\Events\UpdateGoalMilestone;

class UpdateGoalMilestoneLis
{
    public function handle(UpdateGoalMilestone $event)
    {
        if (Module_is_active('ActivityLog')) {
            $milestone = $event->milestone;

            $activity = new AllActivityLog();
            $activity['module'] = 'Goal';
            $activity['sub_module'] = 'Milestone';
            $activity['description'] = __('Goal Milestone updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $milestone->created_by;
            $activity->save();
        }
    }
}