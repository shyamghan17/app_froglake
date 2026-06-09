<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BudgetPlanner\Events\CreateBudgetAllocation;

class CreateBudgetAllocationLis
{
    public function handle(CreateBudgetAllocation $event)
    {
        if (Module_is_active('ActivityLog')) {
            $budgetAllocation = $event->budgetAllocation;

            $activity = new AllActivityLog();
            $activity['module'] = 'BudgetPlanner';
            $activity['sub_module'] = 'Budget Allocation';
            $activity['description'] = __('New Budget Allocation created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $budgetAllocation->created_by;
            $activity->save();
        }
    }
}