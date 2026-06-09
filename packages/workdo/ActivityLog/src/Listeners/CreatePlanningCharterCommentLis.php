<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Planning\Events\CreatePlanningCharterComment;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreatePlanningCharterCommentLis
{
    public function handle(CreatePlanningCharterComment $event)
    {
        if (Module_is_active('ActivityLog')) {

            $activity = new AllActivityLog();
            $activity['module'] = 'Planning';
            $activity['sub_module'] = 'Charter Comment';
            $activity['description'] = __('New Charter Comment added by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = creatorId();
            $activity->save();
        }
    }
}
