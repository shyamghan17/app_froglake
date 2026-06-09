<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\MachineRepairManagement\Events\UpdateMachineRepairRequest;

class UpdateMachineRepairRequestLis
{
    public function handle(UpdateMachineRepairRequest $event)
    {
        if (Module_is_active('ActivityLog')) {
            $machineRepairRequest = $event->machinerepairrequest;

            $activity = new AllActivityLog();
            $activity['module'] = 'MachineRepairManagement';
            $activity['sub_module'] = 'RepairRequest';
            $activity['description'] = __('Machine Repair Request updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $machineRepairRequest->created_by;
            $activity->save();
        }
    }
}
