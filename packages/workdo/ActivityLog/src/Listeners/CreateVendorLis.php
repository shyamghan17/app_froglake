<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Account\Events\CreateVendor;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateVendorLis
{
    public function handle(CreateVendor $event)
    {
        if (Module_is_active('ActivityLog')) {
            $vendor = $event->vendor;

            $activity = new AllActivityLog();
            $activity['module'] = 'Account';
            $activity['sub_module'] = 'Vendor';
            $activity['description'] = __('New Vendor created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $vendor->created_by;
            $activity->save();
        }
    }
}
