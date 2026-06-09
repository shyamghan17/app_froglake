<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Bookings\Events\UpdateBookingPackage;

class UpdateBookingPackageLis
{
    public function handle(UpdateBookingPackage $event)
    {
        if (Module_is_active('ActivityLog')) {
            $package = $event->package;

            $activity = new AllActivityLog();
            $activity['module'] = 'Bookings';
            $activity['sub_module'] = 'Package';
            $activity['description'] = __('Booking Package updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $package->created_by;
            $activity->save();
        }
    }
}
