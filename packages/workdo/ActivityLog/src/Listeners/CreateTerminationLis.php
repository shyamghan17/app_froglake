<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateTermination;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateTerminationLis
{
    public function handle(CreateTermination $event)
    {
        if (Module_is_active('ActivityLog')) {
            $termination = $event->termination;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Termination';
            $activity['description'] = __('New Termination created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $termination->created_by;
            $activity->save();
        }
    }
}
