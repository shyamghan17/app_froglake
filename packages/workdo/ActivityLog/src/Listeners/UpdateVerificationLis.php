<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateVerificationLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $verification = $event->verification;

            $activity = new AllActivityLog();
            $activity['module'] = 'FileSharing';
            $activity['sub_module'] = 'Verification';
            $activity['description'] = __('Verification updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $verification->created_by;
            $activity->save();
        }
    }
}