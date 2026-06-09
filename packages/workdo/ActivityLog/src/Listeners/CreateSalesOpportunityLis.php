<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\Events\CreateSalesOpportunity;

class CreateSalesOpportunityLis
{
    public function handle(CreateSalesOpportunity $event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesOpportunity = $event->opportunity;

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Opportunity';
            $activity['description'] = __('Sales Opportunity created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesOpportunity->created_by;
            $activity->save();
        }
    }
}
