<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Holidayz\Events\HolidayzBookingPayments;

class HolidayzBookingPaymentsLis
{
    public function handle(HolidayzBookingPayments $event)
    {
        $booking = $event->booking;
        if (Module_is_active('ActivityLog',$booking->created_by)) {

            $activity = new AllActivityLog();
            $activity['module'] = 'Holidayz';
            $activity['sub_module'] = 'Booking Payment';
            $activity['description'] = __('Room Booking payment processed by the ');
            $activity['creator_id'] = $booking->creator_id;
            $activity['created_by'] = $booking->created_by;
            $activity->save();
        }
    }
}
