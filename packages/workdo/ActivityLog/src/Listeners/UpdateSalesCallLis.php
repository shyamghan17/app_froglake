<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateSalesCallLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesCall = $event->salesCall;

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Call';
            $activity['description'] = __('Sales Call updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesCall->created_by;
            $activity->save();
        }
    }
}