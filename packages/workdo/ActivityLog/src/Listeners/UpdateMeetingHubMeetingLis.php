<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\MeetingHub\Events\UpdateMeeting;

class UpdateMeetingHubMeetingLis
{
    public function handle(UpdateMeeting $event)
    {
        if (Module_is_active('ActivityLog')) {
            $meeting = $event->meeting;

            $activity = new AllActivityLog();
            $activity['module'] = 'MeetingHub';
            $activity['sub_module'] = 'Meeting';
            $activity['description'] = __('Meeting updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $meeting->created_by;
            $activity->save();
        }
    }
}
