<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class LeadUploadFileLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $leadFile = $event->lead;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Lead';
            $activity['description'] = __('New File Uploaded in lead ') . $leadFile->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $leadFile->created_by;
            $activity->save();
        }
    }
}