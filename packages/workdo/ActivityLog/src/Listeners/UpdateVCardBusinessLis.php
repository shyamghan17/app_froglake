<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\VCard\Events\UpdateBusiness;

class UpdateVCardBusinessLis
{
    public function handle(UpdateBusiness $event)
    {
        if (Module_is_active('ActivityLog')) {
            $business = $event->business;

            $activity = new AllActivityLog();
            $activity['module'] = 'VCard';
            $activity['sub_module'] = 'Business';
            $activity['description'] = __('VCard Business updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $business->created_by;
            $activity->save();
        }
    }
}
