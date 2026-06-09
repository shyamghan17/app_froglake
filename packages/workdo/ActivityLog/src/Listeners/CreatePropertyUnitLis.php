<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\PropertyManagement\Events\CreatePropertyUnit;

class CreatePropertyUnitLis
{
    public function handle(CreatePropertyUnit $event)
    {
        if (Module_is_active('ActivityLog')) {
            $unit = $event->propertyunit;

            $activity = new AllActivityLog();
            $activity['module'] = 'PropertyManagement';
            $activity['sub_module'] = 'Property Unit';
            $activity['description'] = __('New Property Unit created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $unit->created_by;
            $activity->save();
        }
    }
}