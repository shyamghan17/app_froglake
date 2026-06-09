<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Planning\Events\CreatePlanningChallenge;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreatePlanningChallengeLis
{
    public function handle(CreatePlanningChallenge $event)
    {
        if (Module_is_active('ActivityLog')) {
            $challenge = $event->challenge;

            $activity = new AllActivityLog();
            $activity['module'] = 'Planning';
            $activity['sub_module'] = 'Challenge';
            $activity['description'] = __('New Challenge created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $challenge->created_by;
            $activity->save();
        }
    }
}
