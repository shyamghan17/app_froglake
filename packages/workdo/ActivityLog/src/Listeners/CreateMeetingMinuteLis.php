<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\MeetingHub\Events\CreateMeetingMinute;

class CreateMeetingMinuteLis
{
    public function handle(CreateMeetingMinute $event)
    {
        if (Module_is_active('ActivityLog')) {
            $meetingMinute = $event->meetingMinute;

            $activity = new AllActivityLog();
            $activity['module'] = 'MeetingHub';
            $activity['sub_module'] = 'Meeting Minute';
            $activity['description'] = __('New Meeting Minute created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $meetingMinute->created_by;
            $activity->save();
        }
    }
}
