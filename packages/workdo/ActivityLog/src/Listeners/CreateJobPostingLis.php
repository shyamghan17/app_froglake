<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\CreateJobPosting;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateJobPostingLis
{
    public function handle(CreateJobPosting $event)
    {
        if (Module_is_active('ActivityLog')) {
            $jobposting = $event->jobposting;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Job Posting';
            $activity['description'] = __('New Job Posting created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $jobposting->created_by;
            $activity->save();
        }
    }
}
