<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\Events\UpdateBeverageManufacturing;

class UpdateBeverageManufacturingLis
{
    public function handle(UpdateBeverageManufacturing $event)
    {
        if (Module_is_active('ActivityLog')) {
            $manufacturing = $event->beverageManufacturing;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeverageManagement';
            $activity['sub_module'] = 'Manufacturing';
            $activity['description'] = __('Beverage Manufacturing batch updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $manufacturing->created_by;
            $activity->save();
        }
    }
}