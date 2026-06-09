<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreatePayroll;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreatePayrollLis
{
    public function handle(CreatePayroll $event)
    {
        if (Module_is_active('ActivityLog')) {
            $payroll = $event->payroll;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Payroll';
            $activity['description'] = __('New Payroll created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $payroll->created_by;
            $activity->save();
        }
    }
}
