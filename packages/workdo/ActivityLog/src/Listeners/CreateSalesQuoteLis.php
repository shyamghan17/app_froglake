<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateSalesQuoteLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesQuote = $event->quote;

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Quote';
            $activity['description'] = __('Sales Quote created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesQuote->created_by;
            $activity->save();
        }
    }
}