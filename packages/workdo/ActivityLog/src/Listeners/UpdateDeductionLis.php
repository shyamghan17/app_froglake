<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateDeduction;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateDeductionLis
{
    public function handle(UpdateDeduction $event)
    {
        if (Module_is_active('ActivityLog')) {
            $deduction = $event->deduction;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Deduction';
            $activity['description'] = __('Deduction updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $deduction->created_by;
            $activity->save();
        }
    }
}
