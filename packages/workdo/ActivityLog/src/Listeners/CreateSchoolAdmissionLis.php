<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\CreateAdmission;

class CreateSchoolAdmissionLis
{
    public function handle(CreateAdmission $event)
    {
        if (Module_is_active('ActivityLog')) {
            $admission = $event->admission;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Admission';
            $activity['description'] = __('New School Admission created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $admission->created_by;
            $activity->save();
        }
    }
}