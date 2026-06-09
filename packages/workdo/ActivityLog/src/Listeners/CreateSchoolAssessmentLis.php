<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\CreateAssessment;

class CreateSchoolAssessmentLis
{
    public function handle(CreateAssessment $event)
    {
        if (Module_is_active('ActivityLog')) {
            $assessment = $event->assessment;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Assessment';
            $activity['description'] = __('New School Assessment created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $assessment->created_by;
            $activity->save();
        }
    }
}