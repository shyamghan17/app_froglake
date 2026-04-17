<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Taskly\Models\Project;

class CreateProjectMilestoneLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $projectMilestone = $event->milestone;
            $project = Project::find($projectMilestone->project_id);

            $activity = new AllActivityLog();
            $activity['module'] = 'Taskly';
            $activity['sub_module'] = 'Milestone';
            $activity['description'] = __('Project Milestone created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $project->created_by;
            $activity->save();
        }
    }
}