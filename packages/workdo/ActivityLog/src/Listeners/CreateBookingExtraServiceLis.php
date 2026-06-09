<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Bookings\Events\CreateBookingExtraService;

class CreateBookingExtraServiceLis
{
    public function handle(CreateBookingExtraService $event)
    {
        if (Module_is_active('ActivityLog')) {
            $extraService = $event->extraService;

            $activity = new AllActivityLog();
            $activity['module'] = 'Bookings';
            $activity['sub_module'] = 'Extra Service';
            $activity['description'] = __('New Booking Extra Service created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $extraService->created_by;
            $activity->save();
        }
    }
}
