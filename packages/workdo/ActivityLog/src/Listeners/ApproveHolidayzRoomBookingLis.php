<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Holidayz\Events\ApproveHolidayzRoomBooking;

class ApproveHolidayzRoomBookingLis
{
    public function handle(ApproveHolidayzRoomBooking $event)
    {
        if (Module_is_active('ActivityLog')) {
            $booking = $event->booking;

            $activity = new AllActivityLog();
            $activity['module'] = 'Holidayz';
            $activity['sub_module'] = 'Room Booking';
            $activity['description'] = __('Room Booking approved by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $booking->created_by;
            $activity->save();
        }
    }
}