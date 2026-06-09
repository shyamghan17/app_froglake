<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Inventory\Events\ApproveInventoryAdjustment;

class ApproveInventoryAdjustmentLis
{
    public function handle(ApproveInventoryAdjustment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $adjustment = $event->adjustment;

            $activity = new AllActivityLog();
            $activity['module'] = 'Inventory';
            $activity['sub_module'] = 'Inventory Adjustment';
            $activity['description'] = __('Inventory Adjustment approved by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $adjustment->created_by;
            $activity->save();
        }
    }
}
