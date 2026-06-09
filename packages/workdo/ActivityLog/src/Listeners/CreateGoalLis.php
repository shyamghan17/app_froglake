<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Goal\Events\CreateGoal;

class CreateGoalLis
{
    public function handle(CreateGoal $event)
    {
        if (Module_is_active('ActivityLog')) {
            $goal = $event->goal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Goal';
            $activity['sub_module'] = 'Goal';
            $activity['description'] = __('New Goal created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $goal->created_by;
            $activity->save();
        }
    }
}