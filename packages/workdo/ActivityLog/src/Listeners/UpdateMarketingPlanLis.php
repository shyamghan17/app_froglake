<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\MarketingPlan\Events\UpdateMarketingPlan;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateMarketingPlanLis
{
    public function handle(UpdateMarketingPlan $event)
    {
        if (Module_is_active('ActivityLog')) {
            $marketingPlan = $event->marketingPlan;

            $activity = new AllActivityLog();
            $activity['module'] = 'MarketingPlan';
            $activity['sub_module'] = 'Marketing Plan';
            $activity['description'] = __('Marketing Plan updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $marketingPlan->created_by;
            $activity->save();
        }
    }
}
