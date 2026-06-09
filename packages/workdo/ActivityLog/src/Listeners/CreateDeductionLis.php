<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateDeduction;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateDeductionLis
{
    public function handle(CreateDeduction $event)
    {
        if (Module_is_active('ActivityLog')) {
            $deduction = $event->deduction;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Deduction';
            $activity['description'] = __('New Deduction created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $deduction->created_by;
            $activity->save();
        }
    }
}
