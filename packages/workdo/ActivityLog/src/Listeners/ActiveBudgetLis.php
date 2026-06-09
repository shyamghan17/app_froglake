<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BudgetPlanner\Events\ActiveBudget;

class ActiveBudgetLis
{
    public function handle(ActiveBudget $event)
    {
        if (Module_is_active('ActivityLog')) {
            $budget = $event->budget;

            $activity = new AllActivityLog();
            $activity['module'] = 'BudgetPlanner';
            $activity['sub_module'] = 'Budget';
            $activity['description'] = __('Budget activated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $budget->created_by;
            $activity->save();
        }
    }
}