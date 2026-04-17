<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateCleaningBookingLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $cleaningBooking = $event->booking;

            $activity = new AllActivityLog();
            $activity['module'] = 'CleaningManagement';
            $activity['sub_module'] = 'Cleaning Booking';
            $activity['description'] = __('Cleaning Booking updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $cleaningBooking->created_by;
            $activity->save();
        }
    }
}