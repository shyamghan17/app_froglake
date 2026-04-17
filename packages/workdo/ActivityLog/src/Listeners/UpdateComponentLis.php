<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateComponentLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $component = $event->component;

            $activity = new AllActivityLog();
            $activity['module'] = 'CMMS';
            $activity['sub_module'] = 'Component';
            $activity['description'] = __('Component updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $component->created_by;
            $activity->save();
        }
    }
}