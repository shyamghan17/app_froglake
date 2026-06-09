<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\PropertyManagement\Events\CreateProperty;

class CreatePropertyLis
{
    public function handle(CreateProperty $event)
    {
        if (Module_is_active('ActivityLog')) {
            $property = $event->property;

            $activity = new AllActivityLog();
            $activity['module'] = 'PropertyManagement';
            $activity['sub_module'] = 'Property';
            $activity['description'] = __('New Property created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $property->created_by;
            $activity->save();
        }
    }
}