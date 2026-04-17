<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateSalesMeetingLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesMeeting = $event->meeting;

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Meeting';
            $activity['description'] = __('Sales Meeting updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesMeeting->created_by;
            $activity->save();
        }
    }
}