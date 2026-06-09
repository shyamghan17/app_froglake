<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\PropertyManagement\Events\UpdatePropertyUnit;

class UpdatePropertyUnitLis
{
    public function handle(UpdatePropertyUnit $event)
    {
        if (Module_is_active('ActivityLog')) {
            $unit = $event->propertyunit;

            $activity = new AllActivityLog();
            $activity['module'] = 'PropertyManagement';
            $activity['sub_module'] = 'Property Unit';
            $activity['description'] = __('Property Unit updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $unit->created_by;
            $activity->save();
        }
    }
}