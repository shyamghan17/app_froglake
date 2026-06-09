<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Goal\Events\CreateGoalTracking;

class CreateGoalTrackingLis
{
    public function handle(CreateGoalTracking $event)
    {
        if (Module_is_active('ActivityLog')) {
            $tracking = $event->tracking;

            $activity = new AllActivityLog();
            $activity['module'] = 'Goal';
            $activity['sub_module'] = 'Tracking';
            $activity['description'] = __('New Goal Progress tracked by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $tracking->created_by;
            $activity->save();
        }
    }
}