<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\GarageManagement\Events\UpdateService;

class UpdateServiceLis
{
    public function handle(UpdateService $event)
    {
        if (Module_is_active('ActivityLog')) {
            $service = $event->service;

            $activity = new AllActivityLog();
            $activity['module'] = 'GarageManagement';
            $activity['sub_module'] = 'Service';
            $activity['description'] = __('Service updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $service->created_by;
            $activity->save();
        }
    }
}
