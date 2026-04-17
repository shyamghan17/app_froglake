<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateMachineInsuranceLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $machineInsurance = $event->machineinsurance;

            $activity = new AllActivityLog();
            $activity['module'] = 'MachineRepairManagement';
            $activity['sub_module'] = 'Insurance';
            $activity['description'] = __('Machine Insurance created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $machineInsurance->created_by;
            $activity->save();
        }
    }
}