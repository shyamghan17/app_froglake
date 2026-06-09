<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Bookings\Events\CreateBookingPackage;

class CreateBookingPackageLis
{
    public function handle(CreateBookingPackage $event)
    {
        if (Module_is_active('ActivityLog')) {
            $package = $event->package;

            $activity = new AllActivityLog();
            $activity['module'] = 'Bookings';
            $activity['sub_module'] = 'Package';
            $activity['description'] = __('New Booking Package created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $package->created_by;
            $activity->save();
        }
    }
}
