<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Account\Events\CreateExpense;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateExpenseLis
{
    public function handle(CreateExpense $event)
    {
        if (Module_is_active('ActivityLog')) {
            $expense = $event->expense;

            $activity = new AllActivityLog();
            $activity['module'] = 'Account';
            $activity['sub_module'] = 'Expense';
            $activity['description'] = __('New Expense created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $expense->created_by;
            $activity->save();
        }
    }
}
