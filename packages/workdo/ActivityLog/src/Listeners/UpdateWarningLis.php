<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateWarning;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateWarningLis
{
    public function handle(UpdateWarning $event)
    {
        if (Module_is_active('ActivityLog')) {
            $warning = $event->warning;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Warning';
            $activity['description'] = __('Warning updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $warning->created_by;
            $activity->save();
        }
    }
}
