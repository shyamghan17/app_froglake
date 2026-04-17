<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateSalesOrderLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesOrder = $event->salesOrder;

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Order';
            $activity['description'] = __('Sales Order created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesOrder->created_by;
            $activity->save();
        }
    }
}