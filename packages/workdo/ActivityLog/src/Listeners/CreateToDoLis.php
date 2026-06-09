<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\ToDo\Events\CreateToDo;

class CreateToDoLis
{
    public function handle(CreateToDo $event)
    {
        if (Module_is_active('ActivityLog')) {
            $todo = $event->todo;

            $activity = new AllActivityLog();
            $activity['module'] = 'ToDo';
            $activity['sub_module'] = 'ToDo';
            $activity['description'] = __('New ToDo created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $todo->created_by;
            $activity->save();
        }
    }
}
