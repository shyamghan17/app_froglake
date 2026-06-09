<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\CreateCandidateAssessment;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateCandidateAssessmentLis
{
    public function handle(CreateCandidateAssessment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $candidateassessment = $event->candidateAssessment;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Candidate Assessment';
            $activity['description'] = __('New Candidate Assessment created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $candidateassessment->created_by;
            $activity->save();
        }
    }
}
