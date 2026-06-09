<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Events\CreateBeautyService;

class CreateBeautyServiceLis
{
    public function handle(CreateBeautyService $event)
    {
        if (Module_is_active('ActivityLog')) {
            $service = $event->service;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Beauty Service';
            $activity['description'] = __('New Beauty Spa Service created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $service->created_by;
            $activity->save();
        }
    }
}