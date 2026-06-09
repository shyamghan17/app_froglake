<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Performance\Events\CreateEmployeeGoal;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreatePerformanceEmployeeGoalLis
{
    public function handle(CreateEmployeeGoal $event)
    {
        if (Module_is_active('ActivityLog')) {
            $goal = $event->goal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Performance';
            $activity['sub_module'] = 'Employee Goal';
            $activity['description'] = __('New Employee Goal created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $goal->created_by;
            $activity->save();
        }
    }
}