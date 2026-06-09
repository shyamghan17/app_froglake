<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\UpdateCandidateOnboarding;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateCandidateOnboardingLis
{
    public function handle(UpdateCandidateOnboarding $event)
    {
        if (Module_is_active('ActivityLog')) {
            $candidateonboarding = $event->candidateOnboarding;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Candidate Onboarding';
            $activity['description'] = __('Candidate Onboarding updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $candidateonboarding->created_by;
            $activity->save();
        }
    }
}
