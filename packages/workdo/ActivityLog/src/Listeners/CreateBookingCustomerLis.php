<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Bookings\Events\CreateBookingCustomer;

class CreateBookingCustomerLis
{
    public function handle(CreateBookingCustomer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $customer = $event->customer;

            $activity = new AllActivityLog();
            $activity['module'] = 'Bookings';
            $activity['sub_module'] = 'Customer';
            $activity['description'] = __('New Booking Customer created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $customer->created_by;
            $activity->save();
        }
    }
}
