<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateCmmsPosLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $cmmsPos = $event->pos;

            $activity = new AllActivityLog();
            $activity['module'] = 'CMMS';
            $activity['sub_module'] = 'CMMS POS';
            $activity['description'] = __('CMMS POS updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $cmmsPos->created_by;
            $activity->save();
        }
    }
}