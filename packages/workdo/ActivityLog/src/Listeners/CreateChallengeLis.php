<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateChallengeLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $challenge = $event->challenge;

            $activity = new AllActivityLog();
            $activity['module'] = 'Appointment';
            $activity['sub_module'] = 'Challenge';
            $activity['description'] = __('Challenge created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $challenge->created_by;
            $activity->save();
        }
    }
}