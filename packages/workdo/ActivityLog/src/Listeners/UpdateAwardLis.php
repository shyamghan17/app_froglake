<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Hrm\Events\UpdateAward;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateAwardLis
{
    public function handle(UpdateAward $event)
    {
        if (Module_is_active('ActivityLog')) {
            $award = $event->award;

            $activity = new AllActivityLog();
            $activity['module'] = 'HRM';
            $activity['sub_module'] = 'Award';
            $activity['description'] = __('Award updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $award->created_by;
            $activity->save();
        }
    }
}
