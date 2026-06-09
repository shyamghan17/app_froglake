<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Events\CreateBeautyBooking;

class CreateBeautyBookingLis
{
    public function handle(CreateBeautyBooking $event)
    {
        if (Module_is_active('ActivityLog')) {
            $booking = $event->beautybooking;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Beauty Booking';
            $activity['description'] = __('New Beauty Spa Booking created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $booking->created_by;
            $activity->save();
        }
    }
}