<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\VCard\Events\CreateBusiness;

class CreateVCardBusinessLis
{
    public function handle(CreateBusiness $event)
    {
        if (Module_is_active('ActivityLog')) {
            $business = $event->business;

            $activity = new AllActivityLog();
            $activity['module'] = 'VCard';
            $activity['sub_module'] = 'Business';
            $activity['description'] = __('New VCard Business created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $business->created_by;
            $activity->save();
        }
    }
}
