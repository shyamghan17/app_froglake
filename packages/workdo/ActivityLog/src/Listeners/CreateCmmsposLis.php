<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateCmmsposLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $cmmsPos = $event->pos;

            $activity = new AllActivityLog();
            $activity['module'] = 'CMMS';
            $activity['sub_module'] = 'POS';
            $activity['description'] = __('New CMMS POS created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $cmmsPos->created_by;
            $activity->save();
        }
    }
}