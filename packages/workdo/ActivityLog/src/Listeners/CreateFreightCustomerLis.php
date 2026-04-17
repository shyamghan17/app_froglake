<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateFreightCustomerLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $freightCustomer = $event->freightCustomer;

            $activity = new AllActivityLog();
            $activity['module'] = 'FreightManagementSystem';
            $activity['sub_module'] = 'Customer';
            $activity['description'] = __('Freight Customer created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $freightCustomer->created_by;
            $activity->save();
        }
    }
}