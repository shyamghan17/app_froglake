<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Account\Events\UpdateVendor;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateVendorLis
{
    public function handle(UpdateVendor $event)
    {
        if (Module_is_active('ActivityLog')) {
            $vendor = $event->vendor;

            $activity = new AllActivityLog();
            $activity['module'] = 'Account';
            $activity['sub_module'] = 'Vendor';
            $activity['description'] = __('Vendor updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $vendor->created_by;
            $activity->save();
        }
    }
}
