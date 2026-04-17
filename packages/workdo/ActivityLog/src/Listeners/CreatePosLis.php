<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreatePosLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $pos = $event->posSale;

            $activity = new AllActivityLog();
            $activity['module'] = 'Pos';
            $activity['sub_module'] = 'Pos';
            $activity['description'] = __('Pos created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $pos->created_by;
            $activity->save();
        }
    }
}