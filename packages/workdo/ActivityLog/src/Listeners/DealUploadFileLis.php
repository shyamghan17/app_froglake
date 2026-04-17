<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class DealUploadFileLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $dealFile = $event->deal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Deal';
            $activity['description'] = __('Deal file uploaded by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $dealFile->created_by;
            $activity->save();
        }
    }
}