<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Workdo\BeautySpaManagement\Events\BeautyBookingPayments;

class BeautyBookingPaymentsLis
{
    public function handle(BeautyBookingPayments $event)
    {
        $booking = $event->booking;
        if (Module_is_active('ActivityLog',$booking->created_by)) {

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Booking Payment';
            $activity['description'] = __('Beauty Spa Booking payment processed by the ');
            $activity['creator_id'] = $booking->creator_id;
            $activity['created_by'] = $booking->created_by;
            $activity->save();
        }
    }
}
