<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Lead\Events\UpdateLead;

class UpdateLeadLis
{
    public function handle(UpdateLead $event)
    {
        if (Module_is_active('ActivityLog')) {
            $lead = $event->lead;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Lead';
            $activity['description'] = __('Lead Updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $lead->created_by;
            $activity->save();
        }
    }
}
