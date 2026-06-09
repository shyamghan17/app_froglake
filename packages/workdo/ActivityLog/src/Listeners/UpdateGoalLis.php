<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Goal\Events\UpdateGoal;

class UpdateGoalLis
{
    public function handle(UpdateGoal $event)
    {
        if (Module_is_active('ActivityLog')) {
            $goal = $event->goal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Goal';
            $activity['sub_module'] = 'Goal';
            $activity['description'] = __('Goal updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $goal->created_by;
            $activity->save();
        }
    }
}