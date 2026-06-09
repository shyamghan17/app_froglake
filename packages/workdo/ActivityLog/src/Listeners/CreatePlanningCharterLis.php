<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Planning\Events\CreatePlanningCharter;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreatePlanningCharterLis
{
    public function handle(CreatePlanningCharter $event)
    {
        if (Module_is_active('ActivityLog')) {
            $charter = $event->charter;

            $activity = new AllActivityLog();
            $activity['module'] = 'Planning';
            $activity['sub_module'] = 'Charter';
            $activity['description'] = __('New Charter created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $charter->created_by;
            $activity->save();
        }
    }
}
