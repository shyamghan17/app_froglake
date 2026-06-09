<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\MeetingHub\Events\CreateMeeting;

class CreateMeetingHubMeetingLis
{
    public function handle(CreateMeeting $event)
    {
        if (Module_is_active('ActivityLog')) {
            $meeting = $event->meeting;

            $activity = new AllActivityLog();
            $activity['module'] = 'MeetingHub';
            $activity['sub_module'] = 'Meeting';
            $activity['description'] = __('New Meeting created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $meeting->created_by;
            $activity->save();
        }
    }
}
