<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Events\MarkBeautyBookingPaymentPaid;

class MarkBeautyBookingPaymentPaidLis
{
    public function handle(MarkBeautyBookingPaymentPaid $event)
    {
        if (Module_is_active('ActivityLog')) {
            $booking = $event->booking;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Booking Payment';
            $activity['description'] = __('Beauty Spa Booking payment marked as paid by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $booking->created_by;
            $activity->save();
        }
    }
}