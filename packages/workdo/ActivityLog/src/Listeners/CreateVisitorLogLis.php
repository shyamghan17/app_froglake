<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\VisitorManagement\Events\CreateVisitorLog;

class CreateVisitorLogLis
{
    public function handle(CreateVisitorLog $event)
    {
        if (Module_is_active('ActivityLog')) {
            $visitorLog = $event->visitorlog;

            $activity = new AllActivityLog();
            $activity['module'] = 'VisitorManagement';
            $activity['sub_module'] = 'Log';
            $activity['description'] = __('Visitor Log created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $visitorLog->created_by;
            $activity->save();
        }
    }
}
