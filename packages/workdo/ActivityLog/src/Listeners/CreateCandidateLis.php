<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\CreateCandidate;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateCandidateLis
{
    public function handle(CreateCandidate $event)
    {
        if (Module_is_active('ActivityLog')) {
            $candidate = $event->candidate;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Candidate';
            $activity['description'] = __('New Candidate created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $candidate->created_by;
            $activity->save();
        }
    }
}
