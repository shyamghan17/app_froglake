<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdatePayroll;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdatePayrollLis
{
    public function handle(UpdatePayroll $event)
    {
        if (Module_is_active('ActivityLog')) {
            $payroll = $event->payroll;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Payroll';
            $activity['description'] = __('Payroll updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $payroll->created_by;
            $activity->save();
        }
    }
}
