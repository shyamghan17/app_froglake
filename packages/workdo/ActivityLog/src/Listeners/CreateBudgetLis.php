<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BudgetPlanner\Events\CreateBudget;

class CreateBudgetLis
{
    public function handle(CreateBudget $event)
    {
        if (Module_is_active('ActivityLog')) {
            $budget = $event->budget;

            $activity = new AllActivityLog();
            $activity['module'] = 'BudgetPlanner';
            $activity['sub_module'] = 'Budget';
            $activity['description'] = __('New Budget created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $budget->created_by;
            $activity->save();
        }
    }
}