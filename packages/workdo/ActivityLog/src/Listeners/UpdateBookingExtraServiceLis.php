<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Bookings\Events\UpdateBookingExtraService;

class UpdateBookingExtraServiceLis
{
    public function handle(UpdateBookingExtraService $event)
    {
        if (Module_is_active('ActivityLog')) {
            $extraService = $event->extraService;

            $activity = new AllActivityLog();
            $activity['module'] = 'Bookings';
            $activity['sub_module'] = 'Extra Service';
            $activity['description'] = __('Booking Extra Service updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $extraService->created_by;
            $activity->save();
        }
    }
}
