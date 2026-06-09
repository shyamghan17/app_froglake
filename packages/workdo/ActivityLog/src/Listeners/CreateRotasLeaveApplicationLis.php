<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Rotas\Events\CreateLeaveApplication as RotasCreateLeaveApplication;

class CreateRotasLeaveApplicationLis
{
    public function handle(RotasCreateLeaveApplication $event)
    {
        if (Module_is_active('ActivityLog')) {
            $leaveApplication = $event->leaveapplication;

            $activity = new AllActivityLog();
            $activity['module'] = 'Rotas';
            $activity['sub_module'] = 'Leave Application';
            $activity['description'] = __('New Leave Application created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $leaveApplication->created_by;
            $activity->save();
        }
    }
}