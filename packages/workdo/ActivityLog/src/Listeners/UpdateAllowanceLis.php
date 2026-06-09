<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateAllowance;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateAllowanceLis
{
    public function handle(UpdateAllowance $event)
    {
        if (Module_is_active('ActivityLog')) {
            $allowance = $event->allowance;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Allowance';
            $activity['description'] = __('Allowance updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $allowance->created_by;
            $activity->save();
        }
    }
}
