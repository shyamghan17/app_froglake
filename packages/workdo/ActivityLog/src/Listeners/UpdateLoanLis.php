<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateLoan;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateLoanLis
{
    public function handle(UpdateLoan $event)
    {
        if (Module_is_active('ActivityLog')) {
            $loan = $event->loan;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Loan';
            $activity['description'] = __('Loan updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $loan->created_by;
            $activity->save();
        }
    }
}
