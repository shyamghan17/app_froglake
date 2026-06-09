<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateLoan;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateLoanLis
{
    public function handle(CreateLoan $event)
    {
        if (Module_is_active('ActivityLog')) {
            $loan = $event->loan;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Loan';
            $activity['description'] = __('New Loan created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $loan->created_by;
            $activity->save();
        }
    }
}
