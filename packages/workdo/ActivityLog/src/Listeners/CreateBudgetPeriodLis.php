<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BudgetPlanner\Events\CreateBudgetPeriod;

class CreateBudgetPeriodLis
{
    public function handle(CreateBudgetPeriod $event)
    {
        if (Module_is_active('ActivityLog')) {
            $budgetPeriod = $event->budgetPeriod;

            $activity = new AllActivityLog();
            $activity['module'] = 'BudgetPlanner';
            $activity['sub_module'] = 'Budget Period';
            $activity['description'] = __('New Budget Period created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $budgetPeriod->created_by;
            $activity->save();
        }
    }
}
