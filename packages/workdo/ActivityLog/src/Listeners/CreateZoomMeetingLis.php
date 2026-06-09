<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\ZoomMeeting\Events\CreateZoomMeeting;

class CreateZoomMeetingLis
{
    public function handle(CreateZoomMeeting $event)
    {
        if (Module_is_active('ActivityLog')) {
            $meeting = $event->meeting;

            $activity = new AllActivityLog();
            $activity['module'] = 'ZoomMeeting';
            $activity['sub_module'] = 'Zoom Meeting';
            $activity['description'] = __('New Zoom Meeting created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $meeting->created_by;
            $activity->save();
        }
    }
}
