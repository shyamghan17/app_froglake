<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\UpdateJobPosting;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateJobPostingLis
{
    public function handle(UpdateJobPosting $event)
    {
        if (Module_is_active('ActivityLog')) {
            $jobposting = $event->jobposting;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Job Posting';
            $activity['description'] = __('Job Posting updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $jobposting->created_by;
            $activity->save();
        }
    }
}
