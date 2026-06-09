<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\ToDo\Events\CompleteToDo;

class CompleteToDoLis
{
    public function handle(CompleteToDo $event)
    {
        if (Module_is_active('ActivityLog')) {
            $todo = $event->todo;

            $activity = new AllActivityLog();
            $activity['module'] = 'ToDo';
            $activity['sub_module'] = 'ToDo';
            $activity['description'] = __('ToDo completed by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $todo->created_by;
            $activity->save();
        }
    }
}
