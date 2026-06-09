<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\UpdateCandidate;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateCandidateLis
{
    public function handle(UpdateCandidate $event)
    {
        if (Module_is_active('ActivityLog')) {
            $candidate = $event->candidate;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Candidate';
            $activity['description'] = __('Candidate updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $candidate->created_by;
            $activity->save();
        }
    }
}
