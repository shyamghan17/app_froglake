<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\CreateEmployee as SchoolCreateEmployee;

class CreateSchoolEmployeeLis
{
    public function handle(SchoolCreateEmployee $event)
    {
        if (Module_is_active('ActivityLog')) {
            $employee = $event->employee;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Employee';
            $activity['description'] = __('New School Employee created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $employee->created_by;
            $activity->save();
        }
    }
}