<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Goal\Events\CreateGoalContribution;

class CreateGoalContributionLis
{
    public function handle(CreateGoalContribution $event)
    {
        if (Module_is_active('ActivityLog')) {
            $contribution = $event->goalContribution;

            $activity = new AllActivityLog();
            $activity['module'] = 'Goal';
            $activity['sub_module'] = 'Contribution';
            $activity['description'] = __('New Goal Contribution created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $contribution->created_by;
            $activity->save();
        }
    }
}
