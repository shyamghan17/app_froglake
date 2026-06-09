<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Account\Events\CreateCustomer;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateCustomerLis
{
    public function handle(CreateCustomer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $customer = $event->customer;

            $activity = new AllActivityLog();
            $activity['module'] = 'Account';
            $activity['sub_module'] = 'Customer';
            $activity['description'] = __('New Customer created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $customer->created_by;
            $activity->save();
        }
    }
}
