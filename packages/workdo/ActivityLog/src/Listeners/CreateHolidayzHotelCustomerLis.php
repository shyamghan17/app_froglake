<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Holidayz\Events\CreateHolidayzHotelCustomer;

class CreateHolidayzHotelCustomerLis
{
    public function handle(CreateHolidayzHotelCustomer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $customer = $event->customer;

            $activity = new AllActivityLog();
            $activity['module'] = 'Holidayz';
            $activity['sub_module'] = 'Hotel Customer';
            $activity['description'] = __('New Hotel Customer created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $customer->created_by;
            $activity->save();
        }
    }
}