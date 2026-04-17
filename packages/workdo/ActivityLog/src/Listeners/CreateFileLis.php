<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateFileLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $file = $event->file;

            $activity = new AllActivityLog();
            $activity['module'] = 'FileSharing';
            $activity['sub_module'] = 'File';
            $activity['description'] = __('File created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $file->created_by;
            $activity->save();
        }
    }
}