<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BusinessProcessMapping\Events\UpdateBusinessProcessMapping;

class UpdateBusinessProcessMappingLis
{
    public function handle(UpdateBusinessProcessMapping $event)
    {
        if (Module_is_active('ActivityLog')) {
            $businessProcessMapping = $event->businessprocessmapping;

            $activity = new AllActivityLog();
            $activity['module'] = 'BusinessProcessMapping';
            $activity['sub_module'] = 'Business Process Mapping';
            $activity['description'] = __('Business Process Mapping updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $businessProcessMapping->created_by;
            $activity->save();
        }
    }
}
