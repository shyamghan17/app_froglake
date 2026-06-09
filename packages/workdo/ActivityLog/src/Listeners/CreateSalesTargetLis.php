<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\SalesAgent\Events\CreateSalesTarget;

class CreateSalesTargetLis
{
    public function handle(CreateSalesTarget $event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesTarget = $event->salesTarget;

            $activity = new AllActivityLog();
            $activity['module'] = 'SalesAgent';
            $activity['sub_module'] = 'Sales Target';
            $activity['description'] = __('New Sales Target created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesTarget->created_by;
            $activity->save();
        }
    }
}