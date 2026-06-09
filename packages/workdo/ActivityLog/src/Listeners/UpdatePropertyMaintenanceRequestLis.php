<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\PropertyManagement\Events\UpdatePropertyMaintenanceRequest;

class UpdatePropertyMaintenanceRequestLis
{
    public function handle(UpdatePropertyMaintenanceRequest $event)
    {
        if (Module_is_active('ActivityLog')) {
            $maintenanceRequest = $event->propertymaintenancerequest;

            $activity = new AllActivityLog();
            $activity['module'] = 'PropertyManagement';
            $activity['sub_module'] = 'Maintenance Request';
            $activity['description'] = __('Property Maintenance Request updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $maintenanceRequest->created_by;
            $activity->save();
        }
    }
}