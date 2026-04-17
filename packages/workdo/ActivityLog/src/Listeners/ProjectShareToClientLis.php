<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class ProjectShareToClientLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $project = $event->project;

            $activity = new AllActivityLog();
            $activity['module'] = 'Taskly';
            $activity['sub_module'] = 'Share';
            $activity['description'] = __('Project shared to client by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $project->created_by;
            $activity->save();
        }
    }
}