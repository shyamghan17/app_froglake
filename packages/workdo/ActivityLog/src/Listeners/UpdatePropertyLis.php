<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\PropertyManagement\Events\UpdateProperty;

class UpdatePropertyLis
{
    public function handle(UpdateProperty $event)
    {
        if (Module_is_active('ActivityLog')) {
            $property = $event->property;

            $activity = new AllActivityLog();
            $activity['module'] = 'PropertyManagement';
            $activity['sub_module'] = 'Property';
            $activity['description'] = __('Property updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $property->created_by;
            $activity->save();
        }
    }
}