<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateResignation;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateResignationLis
{
    public function handle(UpdateResignation $event)
    {
        if (Module_is_active('ActivityLog')) {
            $resignation = $event->resignation;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Resignation';
            $activity['description'] = __('Resignation updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $resignation->created_by;
            $activity->save();
        }
    }
}
