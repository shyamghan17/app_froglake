<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Account\Events\CreateRevenue;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateRevenueLis
{
    public function handle(CreateRevenue $event)
    {
        if (Module_is_active('ActivityLog')) {
            $revenue = $event->revenue;

            $activity = new AllActivityLog();
            $activity['module'] = 'Account';
            $activity['sub_module'] = 'Revenue';
            $activity['description'] = __('New Revenue created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $revenue->created_by;
            $activity->save();
        }
    }
}
