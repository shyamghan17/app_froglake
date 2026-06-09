<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeautySpaManagement\Events\UpdateBeautyMembership;

class UpdateBeautyMembershipLis
{
    public function handle(UpdateBeautyMembership $event)
    {
        if (Module_is_active('ActivityLog')) {
            $membership = $event->beautymembership;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeautySpaManagement';
            $activity['sub_module'] = 'Beauty Membership';
            $activity['description'] = __('Beauty Spa Membership updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $membership->created_by;
            $activity->save();
        }
    }
}