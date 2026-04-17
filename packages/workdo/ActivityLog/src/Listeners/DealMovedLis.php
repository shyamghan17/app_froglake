<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class DealMovedLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $deal = $event->deal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Deal';
            $activity['description'] = __('Deal moved by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $deal->created_by;
            $activity->save();
        }
    }
}
