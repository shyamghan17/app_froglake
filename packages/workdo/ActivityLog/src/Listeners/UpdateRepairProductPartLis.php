<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateRepairProductPartLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $repairProductPart = $event->repairPart;

            $activity = new AllActivityLog();
            $activity['module'] = 'RepairManagementSystem';
            $activity['sub_module'] = 'Product Part';
            $activity['description'] = __('Repair Product Part updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $repairProductPart->created_by;
            $activity->save();
        }
    }
}