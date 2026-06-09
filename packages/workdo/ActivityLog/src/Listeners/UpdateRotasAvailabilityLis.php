<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Rotas\Events\UpdateAvailability;

class UpdateRotasAvailabilityLis
{
    public function handle(UpdateAvailability $event)
    {
        if (Module_is_active('ActivityLog')) {
            $availability = $event->availability;

            $activity = new AllActivityLog();
            $activity['module'] = 'Rotas';
            $activity['sub_module'] = 'Availability';
            $activity['description'] = __('Employee Availability updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $availability->created_by;
            $activity->save();
        }
    }
}