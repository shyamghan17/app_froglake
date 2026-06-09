<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateEmployee;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateEmployeeLis
{
    public function handle(UpdateEmployee $event)
    {
        if (Module_is_active('ActivityLog')) {
            $employee = $event->employee;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Employee';
            $activity['description'] = __('Employee updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $employee->created_by;
            $activity->save();
        }
    }
}
