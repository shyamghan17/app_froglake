<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateMachineLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $machine = $event->machine;

            $activity = new AllActivityLog();
            $activity['module'] = 'MachineRepairManagement';
            $activity['sub_module'] = 'Machine';
            $activity['description'] = __('Machine updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $machine->created_by;
            $activity->save();
        }
    }
}