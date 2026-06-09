<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\MeetingHub\Events\UpdateMeetingMinuteTask;

class UpdateMeetingMinuteTaskLis
{
    public function handle(UpdateMeetingMinuteTask $event)
    {
        if (Module_is_active('ActivityLog')) {
            $meetingMinuteTask = $event->meetingMinuteTask;

            $activity = new AllActivityLog();
            $activity['module'] = 'MeetingHub';
            $activity['sub_module'] = 'Meeting Minute Task';
            $activity['description'] = __('Meeting Minute Task updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $meetingMinuteTask->created_by;
            $activity->save();
        }
    }
}
