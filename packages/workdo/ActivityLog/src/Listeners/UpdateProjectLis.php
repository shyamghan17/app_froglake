<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Taskly\Events\UpdateProject;

class UpdateProjectLis
{
    public function handle(UpdateProject $event)
    {
        if (Module_is_active('ActivityLog')) {
            $project = $event->project;

            $activity = new AllActivityLog();
            $activity['module'] = 'Taskly';
            $activity['sub_module'] = 'Project';
            $activity['description'] = __('Project updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $project->created_by;
            $activity->save();
        }
    }
}
