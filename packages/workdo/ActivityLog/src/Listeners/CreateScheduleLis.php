<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateScheduleLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $schedule = $event->schedule;

            $activity = new AllActivityLog();
            $activity['module'] = 'Appointment';
            $activity['sub_module'] = 'Schedule';
            $activity['description'] = __('New Schedule created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $schedule->created_by;
            $activity->save();
        }
    }
}