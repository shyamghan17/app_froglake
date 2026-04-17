<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateFreightBookingRequestLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $freightBookingRequest = $event->freightBookingRequest;

            $activity = new AllActivityLog();
            $activity['module'] = 'FreightManagementSystem';
            $activity['sub_module'] = 'BookingRequest';
            $activity['description'] = __('Freight Booking Request created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $freightBookingRequest->created_by;
            $activity->save();
        }
    }
}