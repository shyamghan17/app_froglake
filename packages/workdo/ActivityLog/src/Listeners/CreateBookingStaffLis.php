<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Bookings\Events\CreateBookingStaff;

class CreateBookingStaffLis
{
    public function handle(CreateBookingStaff $event)
    {
        if (Module_is_active('ActivityLog')) {
            $staff = $event->staff;

            $activity = new AllActivityLog();
            $activity['module'] = 'Bookings';
            $activity['sub_module'] = 'Staff';
            $activity['description'] = __('New Booking Staff created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $staff->created_by;
            $activity->save();
        }
    }
}
