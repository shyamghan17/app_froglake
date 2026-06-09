<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Rotas\Events\CreateEmployee as RotasCreateEmployee;

class CreateRotasEmployeeLis
{
    public function handle(RotasCreateEmployee $event)
    {
        if (Module_is_active('ActivityLog')) {
            $employee = $event->employee;

            $activity = new AllActivityLog();
            $activity['module'] = 'Rotas';
            $activity['sub_module'] = 'Employee';
            $activity['description'] = __('New Rotas Employee created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $employee->created_by;
            $activity->save();
        }
    }
}