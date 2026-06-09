<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Events\CreateBeautyMembership;

class CreateBeautyMembershipLis
{
    public function handle(CreateBeautyMembership $event)
    {
        if (Module_is_active('ActivityLog')) {
            $membership = $event->beautymembership;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Beauty Membership';
            $activity['description'] = __('New Beauty Spa Membership created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $membership->created_by;
            $activity->save();
        }
    }
}