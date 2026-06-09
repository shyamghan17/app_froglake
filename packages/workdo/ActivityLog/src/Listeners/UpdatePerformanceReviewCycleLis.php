<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Performance\Events\UpdateReviewCycle;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdatePerformanceReviewCycleLis
{
    public function handle(UpdateReviewCycle $event)
    {
        if (Module_is_active('ActivityLog')) {
            $cycle = $event->cycle;

            $activity = new AllActivityLog();
            $activity['module'] = 'Performance';
            $activity['sub_module'] = 'Review Cycle';
            $activity['description'] = __('Review Cycle updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $cycle->created_by;
            $activity->save();
        }
    }
}