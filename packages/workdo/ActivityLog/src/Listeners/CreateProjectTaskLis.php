<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Taskly\Models\Project;

class CreateProjectTaskLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $projectTask = $event->task;
            $project = Project::find($projectTask->project_id);

            $activity = new AllActivityLog();
            $activity['module'] = 'Taskly';
            $activity['sub_module'] = 'Task';
            $activity['description'] = __('New Task ') . $projectTask->title . __(' created in project ') . $project->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $project->created_by;
            $activity->save();
        }
    }
}