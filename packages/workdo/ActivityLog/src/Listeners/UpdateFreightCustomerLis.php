<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\FreightManagementSystem\Events\UpdateFreightCustomer;

class UpdateFreightCustomerLis
{
    public function handle(UpdateFreightCustomer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $freightCustomer = $event->freightCustomer;

            $activity = new AllActivityLog();
            $activity['module'] = 'FreightManagementSystem';
            $activity['sub_module'] = 'Customer';
            $activity['description'] = __('Freight Customer updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $freightCustomer->created_by;
            $activity->save();
        }
    }
}
