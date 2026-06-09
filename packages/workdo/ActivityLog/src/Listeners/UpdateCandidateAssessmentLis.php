<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\UpdateCandidateAssessment;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateCandidateAssessmentLis
{
    public function handle(UpdateCandidateAssessment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $candidateassessment = $event->candidateAssessment;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Candidate Assessment';
            $activity['description'] = __('Candidate Assessment updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $candidateassessment->created_by;
            $activity->save();
        }
    }
}
