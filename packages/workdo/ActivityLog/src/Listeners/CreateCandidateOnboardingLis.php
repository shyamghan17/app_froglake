<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Recruitment\Events\CreateCandidateOnboarding;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateCandidateOnboardingLis
{
    public function handle(CreateCandidateOnboarding $event)
    {
        if (Module_is_active('ActivityLog')) {
            $candidateonboarding = $event->candidateOnboarding;

            $activity = new AllActivityLog();
            $activity['module'] = 'Recruitment';
            $activity['sub_module'] = 'Candidate Onboarding';
            $activity['description'] = __('New Candidate Onboarding created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $candidateonboarding->created_by;
            $activity->save();
        }
    }
}
