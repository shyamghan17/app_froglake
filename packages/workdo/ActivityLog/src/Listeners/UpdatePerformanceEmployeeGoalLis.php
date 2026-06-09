<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Performance\Events\UpdateEmployeeGoal;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdatePerformanceEmployeeGoalLis
{
    public function handle(UpdateEmployeeGoal $event)
    {
        if (Module_is_active('ActivityLog')) {
            $goal = $event->goal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Performance';
            $activity['sub_module'] = 'Employee Goal';
            $activity['description'] = __('Employee Goal updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $goal->created_by;
            $activity->save();
        }
    }
}