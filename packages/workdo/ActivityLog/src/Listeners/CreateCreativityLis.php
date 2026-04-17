<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateCreativityLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $creativity = $event->creativity;

            $activity = new AllActivityLog();
            $activity['module'] = 'InnovationCenter';
            $activity['sub_module'] = 'Creativity';
            $activity['description'] = __('Creativity created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $creativity->created_by;
            $activity->save();
        }
    }
}