<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BudgetPlanner\Events\ApproveBudgetPeriod;

class ApproveBudgetPeriodLis
{
    public function handle(ApproveBudgetPeriod $event)
    {
        if (Module_is_active('ActivityLog')) {
            $budgetPeriod = $event->budget_period;

            $activity = new AllActivityLog();
            $activity['module'] = 'BudgetPlanner';
            $activity['sub_module'] = 'Budget Period';
            $activity['description'] = __('Budget Period approved by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $budgetPeriod->created_by;
            $activity->save();
        }
    }
}
