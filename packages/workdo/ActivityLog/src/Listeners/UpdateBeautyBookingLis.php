<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Events\UpdateBeautyBooking;

class UpdateBeautyBookingLis
{
    public function handle(UpdateBeautyBooking $event)
    {
        if (Module_is_active('ActivityLog')) {
            $booking = $event->beautybooking;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Beauty Booking';
            $activity['description'] = __('Beauty Spa Booking updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $booking->created_by;
            $activity->save();
        }
    }
}