<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Rotas\Events\UpdateLeaveApplication as RotasUpdateLeaveApplication;

class UpdateRotasLeaveApplicationLis
{
    public function handle(RotasUpdateLeaveApplication $event)
    {
        if (Module_is_active('ActivityLog')) {
            $leaveApplication = $event->leaveapplication;

            $activity = new AllActivityLog();
            $activity['module'] = 'Rotas';
            $activity['sub_module'] = 'Leave Application';
            $activity['description'] = __('Leave Application updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $leaveApplication->created_by;
            $activity->save();
        }
    }
}