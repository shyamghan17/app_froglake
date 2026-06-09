<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\SalesAgent\Events\UpdateSalesAgent;

class UpdateSalesAgentLis
{
    public function handle(UpdateSalesAgent $event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesAgent = $event->salesAgent;

            $activity = new AllActivityLog();
            $activity['module'] = 'SalesAgent';
            $activity['sub_module'] = 'Sales Agent';
            $activity['description'] = __('Sales Agent updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesAgent->created_by;
            $activity->save();
        }
    }
}