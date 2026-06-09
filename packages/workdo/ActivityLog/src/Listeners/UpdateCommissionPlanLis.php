<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Commission\Events\UpdateCommissionPlan;

class UpdateCommissionPlanLis
{
    public function handle(UpdateCommissionPlan $event)
    {
        if (Module_is_active('ActivityLog')) {
            $commissionPlan = $event->commissionPlan;

            $activity = new AllActivityLog();
            $activity['module'] = 'Commission';
            $activity['sub_module'] = 'Commission Plan';
            $activity['description'] = __('Commission Plan updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $commissionPlan->created_by;
            $activity->save();
        }
    }
}
