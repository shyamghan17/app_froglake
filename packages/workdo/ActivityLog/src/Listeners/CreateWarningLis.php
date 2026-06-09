<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateWarning;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateWarningLis
{
    public function handle(CreateWarning $event)
    {
        if (Module_is_active('ActivityLog')) {
            $warning = $event->warning;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Warning';
            $activity['description'] = __('New Warning created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $warning->created_by;
            $activity->save();
        }
    }
}
