<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class DealCallUpdateLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $dealCall = $event->dealCall;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Deal';
            $activity['description'] = __('Deal Call updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $dealCall->created_by;
            $activity->save();
        }
    }
}