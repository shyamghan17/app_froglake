<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\VisitorManagement\Events\UpdateVisitor;

class UpdateVisitorLis
{
    public function handle(UpdateVisitor $event)
    {
        if (Module_is_active('ActivityLog')) {
            $visitor = $event->visitor;

            $activity = new AllActivityLog();
            $activity['module'] = 'VisitorManagement';
            $activity['sub_module'] = 'Visitor';
            $activity['description'] = __('Visitor updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $visitor->created_by;
            $activity->save();
        }
    }
}
