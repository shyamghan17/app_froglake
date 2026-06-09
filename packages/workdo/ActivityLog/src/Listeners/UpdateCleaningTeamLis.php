<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\CleaningManagement\Events\UpdateCleaningTeam;

class UpdateCleaningTeamLis
{
    public function handle(UpdateCleaningTeam $event)
    {
        if (Module_is_active('ActivityLog')) {
            $cleaningTeam = $event->team;

            $activity = new AllActivityLog();
            $activity['module'] = 'CleaningManagement';
            $activity['sub_module'] = 'Cleaning Team';
            $activity['description'] = __('Cleaning Team updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $cleaningTeam->created_by;
            $activity->save();
        }
    }
}
