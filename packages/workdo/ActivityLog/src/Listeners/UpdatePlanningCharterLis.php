<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Planning\Events\UpdatePlanningCharter;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdatePlanningCharterLis
{
    public function handle(UpdatePlanningCharter $event)
    {
        if (Module_is_active('ActivityLog')) {
            $charter = $event->charter;

            $activity = new AllActivityLog();
            $activity['module'] = 'Planning';
            $activity['sub_module'] = 'Charter';
            $activity['description'] = __('Charter updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $charter->created_by;
            $activity->save();
        }
    }
}
