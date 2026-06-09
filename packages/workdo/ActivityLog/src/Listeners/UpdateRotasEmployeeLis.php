<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Rotas\Events\UpdateEmployee as RotasUpdateEmployee;

class UpdateRotasEmployeeLis
{
    public function handle(RotasUpdateEmployee $event)
    {
        if (Module_is_active('ActivityLog')) {
            $employee = $event->employee;

            $activity = new AllActivityLog();
            $activity['module'] = 'Rotas';
            $activity['sub_module'] = 'Employee';
            $activity['description'] = __('Rotas Employee updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $employee->created_by;
            $activity->save();
        }
    }
}