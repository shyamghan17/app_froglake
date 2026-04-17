<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateSalesQuoteLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesQuote = $event->quote;

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Quote';
            $activity['description'] = __('Sales Quote updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesQuote->created_by;
            $activity->save();
        }
    }
}