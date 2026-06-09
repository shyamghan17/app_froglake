<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Performance\Events\UpdateEmployeeReview;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdatePerformanceEmployeeReviewLis
{
    public function handle(UpdateEmployeeReview $event)
    {
        if (Module_is_active('ActivityLog')) {
            $review = $event->review;

            $activity = new AllActivityLog();
            $activity['module'] = 'Performance';
            $activity['sub_module'] = 'Employee Review';
            $activity['description'] = __('Employee Review updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $review->created_by;
            $activity->save();
        }
    }
}