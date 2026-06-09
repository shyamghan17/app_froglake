<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\CleaningManagement\Events\CreateCleaningTeam;

class CreateCleaningTeamLis
{
    public function handle(CreateCleaningTeam $event)
    {
        if (Module_is_active('ActivityLog')) {
            $cleaningTeam = $event->cleaningTeam;

            $activity = new AllActivityLog();
            $activity['module'] = 'CleaningManagement';
            $activity['sub_module'] = 'Cleaning Team';
            $activity['description'] = __('New Cleaning Team created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $cleaningTeam->created_by;
            $activity->save();
        }
    }
}
