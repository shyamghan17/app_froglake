<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Training\Events\CreateTrainingTask;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateTrainingTaskLis
{
    public function handle(CreateTrainingTask $event)
    {
        if (Module_is_active('ActivityLog')) {
            $task = $event->task;

            $activity = new AllActivityLog();
            $activity['module'] = 'Training';
            $activity['sub_module'] = 'Training Task';
            $activity['description'] = __('New Training Task created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $task->created_by;
            $activity->save();
        }
    }
}