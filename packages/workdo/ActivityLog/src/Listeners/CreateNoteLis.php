<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateNoteLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $note = $event->note;

            $activity = new AllActivityLog();
            $activity['module'] = 'Notes';
            $activity['sub_module'] = 'Note';
            $activity['description'] = __('Note created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $note->created_by;
            $activity->save();
        }
    }
}