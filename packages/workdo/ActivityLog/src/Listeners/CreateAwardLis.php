<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\CreateAward;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateAwardLis
{
    public function handle(CreateAward $event)
    {
        if (Module_is_active('ActivityLog')) {
            $award = $event->award;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Award';
            $activity['description'] = __('New Award created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $award->created_by;
            $activity->save();
        }
    }
}
