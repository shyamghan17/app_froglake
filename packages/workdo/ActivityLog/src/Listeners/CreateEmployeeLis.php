<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateEmployee;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateEmployeeLis
{
    public function handle(CreateEmployee $event)
    {
        if (Module_is_active('ActivityLog')) {
            $employee = $event->employee;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Employee';
            $activity['description'] = __('New Employee created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $employee->created_by;
            $activity->save();
        }
    }
}
