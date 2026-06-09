<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Account\Events\UpdateExpense;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateExpenseLis
{
    public function handle(UpdateExpense $event)
    {
        if (Module_is_active('ActivityLog')) {
            $expense = $event->expense;

            $activity = new AllActivityLog();
            $activity['module'] = 'Account';
            $activity['sub_module'] = 'Expense';
            $activity['description'] = __('Expense updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $expense->created_by;
            $activity->save();
        }
    }
}
