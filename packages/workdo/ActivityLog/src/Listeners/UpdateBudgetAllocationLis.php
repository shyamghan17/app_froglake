<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BudgetPlanner\Events\UpdateBudgetAllocation;

class UpdateBudgetAllocationLis
{
    public function handle(UpdateBudgetAllocation $event)
    {
        if (Module_is_active('ActivityLog')) {
            $budgetAllocation = $event->budget_allocation   ;

            $activity = new AllActivityLog();
            $activity['module'] = 'BudgetPlanner';
            $activity['sub_module'] = 'Budget Allocation';
            $activity['description'] = __('Budget Allocation updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $budgetAllocation->created_by;
            $activity->save();
        }
    }
}
