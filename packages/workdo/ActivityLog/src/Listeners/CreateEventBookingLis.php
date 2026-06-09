<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\EventsManagement\Events\CreateEventBooking;

class CreateEventBookingLis
{
    public function handle(CreateEventBooking $event)
    {
        if (Module_is_active('ActivityLog')) {
            $booking = $event->eventBooking;

            $activity = new AllActivityLog();
            $activity['module'] = 'EventsManagement';
            $activity['sub_module'] = 'Event Booking';
            $activity['description'] = __('Event Booking created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $booking->created_by;
            $activity->save();
        }
    }
}