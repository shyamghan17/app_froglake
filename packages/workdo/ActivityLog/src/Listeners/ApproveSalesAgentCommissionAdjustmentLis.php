<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\SalesAgent\Events\ApproveSalesAgentCommissionAdjustment;

class ApproveSalesAgentCommissionAdjustmentLis
{
    public function handle(ApproveSalesAgentCommissionAdjustment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $adjustment = $event->adjustment;

            $activity = new AllActivityLog();
            $activity['module'] = 'SalesAgent';
            $activity['sub_module'] = 'Commission Adjustment';
            $activity['description'] = __('Sales Agent Commission Adjustment approved by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $adjustment->created_by;
            $activity->save();
        }
    }
}