<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\PropertyManagement\Events\UpdatePropertyInspection;

class UpdatePropertyInspectionLis
{
    public function handle(UpdatePropertyInspection $event)
    {
        if (Module_is_active('ActivityLog')) {
            $inspection = $event->propertyinspection;

            $activity = new AllActivityLog();
            $activity['module'] = 'PropertyManagement';
            $activity['sub_module'] = 'Property Inspection';
            $activity['description'] = __('Property Inspection updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $inspection->created_by;
            $activity->save();
        }
    }
}