<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\PropertyManagement\Events\UpdatePropertyTenant;

class UpdatePropertyTenantLis
{
    public function handle(UpdatePropertyTenant $event)
    {
        if (Module_is_active('ActivityLog')) {
            $tenant = $event->propertytenant;

            $activity = new AllActivityLog();
            $activity['module'] = 'PropertyManagement';
            $activity['sub_module'] = 'Property Tenant';
            $activity['description'] = __('Property Tenant updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $tenant->created_by;
            $activity->save();
        }
    }
}