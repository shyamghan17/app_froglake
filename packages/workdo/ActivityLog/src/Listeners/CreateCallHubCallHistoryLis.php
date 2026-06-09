<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\CallHub\Events\CreateCallHubCallHistory;

class CreateCallHubCallHistoryLis
{
    public function handle(CreateCallHubCallHistory $event)
    {
        if (Module_is_active('ActivityLog')) {
            $callHistory = $event->callHistory;

            $activity = new AllActivityLog();
            $activity['module'] = 'CallHub';
            $activity['sub_module'] = 'Call History';
            $activity['description'] = __('New Call History created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $callHistory->created_by;
            $activity->save();
        }
    }
}
