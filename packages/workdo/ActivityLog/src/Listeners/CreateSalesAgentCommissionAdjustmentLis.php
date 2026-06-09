<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\SalesAgent\Events\CreateSalesAgentCommissionAdjustment;

class CreateSalesAgentCommissionAdjustmentLis
{
    public function handle(CreateSalesAgentCommissionAdjustment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $adjustment = $event->adjustment;

            $activity = new AllActivityLog();
            $activity['module'] = 'SalesAgent';
            $activity['sub_module'] = 'Commission Adjustment';
            $activity['description'] = __('New Sales Agent Commission Adjustment created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $adjustment->created_by;
            $activity->save();
        }
    }
}