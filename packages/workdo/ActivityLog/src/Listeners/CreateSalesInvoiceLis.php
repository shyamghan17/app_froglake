<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateSalesInvoiceLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesInvoice = $event->salesInvoice;

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Invoice';
            $activity['description'] = __('Sales Invoice created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesInvoice->created_by;
            $activity->save();
        }
    }
}