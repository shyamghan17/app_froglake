<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\SalesAgent\Events\UpdateSalesAgentCommissionPlan;

class UpdateSalesAgentCommissionPlanLis
{
    public function handle(UpdateSalesAgentCommissionPlan $event)
    {
        if (Module_is_active('ActivityLog')) {
            $commissionPlan = $event->salesAgentCommissionPlan;

            $activity = new AllActivityLog();
            $activity['module'] = 'SalesAgent';
            $activity['sub_module'] = 'Commission Plan';
            $activity['description'] = __('Sales Agent Commission Plan updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $commissionPlan->created_by;
            $activity->save();
        }
    }
}