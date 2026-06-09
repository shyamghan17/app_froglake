<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\EventsManagement\Events\EventBookingPayments;

class EventBookingPaymentsLis
{
    public function handle(EventBookingPayments $event)
    {
        if (Module_is_active('ActivityLog')) {
            $payment = $event->eventBookingPayment;

            $activity = new AllActivityLog();
            $activity['module'] = 'EventsManagement';
            $activity['sub_module'] = 'Event Payment';
            $activity['description'] = __('Event Booking Payment processed by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $payment->created_by;
            $activity->save();
        }
    }
}