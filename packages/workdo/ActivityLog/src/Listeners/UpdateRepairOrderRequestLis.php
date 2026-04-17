<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateRepairOrderRequestLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $repairOrderRequest = $event->repairOrderRequest;

            $activity = new AllActivityLog();
            $activity['module'] = 'RepairManagementSystem';
            $activity['sub_module'] = 'Request';
            $activity['description'] = __('Repair Order Request updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $repairOrderRequest->created_by;
            $activity->save();
        }
    }
}