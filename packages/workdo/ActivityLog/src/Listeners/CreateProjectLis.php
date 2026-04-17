<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateProjectLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $project = $event->project;

            $activity = new AllActivityLog();
            $activity['module'] = 'Taskly';
            $activity['sub_module'] = 'Project';
            $activity['description'] = __('Project created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $project->created_by;
            $activity->save();
        }
    }
}