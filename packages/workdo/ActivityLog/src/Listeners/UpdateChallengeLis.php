<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\InnovationCenter\Events\UpdateChallenge;

class UpdateChallengeLis
{
    public function handle(UpdateChallenge $event)
    {
        if (Module_is_active('ActivityLog')) {
            $challenge = $event->challenge;

            $activity = new AllActivityLog();
            $activity['module'] = 'InnovationCenter';
            $activity['sub_module'] = 'Challenge';
            $activity['description'] = __('Challenge updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $challenge->created_by;
            $activity->save();
        }
    }
}
