<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateAllowance;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateAllowanceLis
{
    public function handle(CreateAllowance $event)
    {
        if (Module_is_active('ActivityLog')) {
            $allowance = $event->allowance;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Allowance';
            $activity['description'] = __('New Allowance created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $allowance->created_by;
            $activity->save();
        }
    }
}
