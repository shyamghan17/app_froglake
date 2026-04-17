<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateVisitorLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $visitor = $event->visitor;

            $activity = new AllActivityLog();
            $activity['module'] = 'VisitorManagement';
            $activity['sub_module'] = 'Visitor';
            $activity['description'] = __('Visitor created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $visitor->created_by;
            $activity->save();
        }
    }
}