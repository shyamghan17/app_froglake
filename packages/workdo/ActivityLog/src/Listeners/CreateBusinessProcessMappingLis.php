<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BusinessProcessMapping\Events\CreateBusinessProcessMapping;

class CreateBusinessProcessMappingLis
{
    public function handle(CreateBusinessProcessMapping $event)
    {
        if (Module_is_active('ActivityLog')) {
            $businessProcessMapping = $event->businessprocessmapping;

            $activity = new AllActivityLog();
            $activity['module'] = 'BusinessProcessMapping';
            $activity['sub_module'] = 'Business Process Mapping';
            $activity['description'] = __('New Business Process Mapping created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $businessProcessMapping->created_by;
            $activity->save();
        }
    }
}
