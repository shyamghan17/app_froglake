<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateCleaningBookingLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $cleaningBooking = $event->cleaningBooking;

            $activity = new AllActivityLog();
            $activity['module'] = 'CleaningManagement';
            $activity['sub_module'] = 'Cleaning Booking';
            $activity['description'] = __('New Cleaning Booking created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $cleaningBooking->created_by;
            $activity->save();
        }
    }
}