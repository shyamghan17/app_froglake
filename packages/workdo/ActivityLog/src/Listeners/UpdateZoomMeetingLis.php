<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\ZoomMeeting\Events\UpdateZoomMeeting;

class UpdateZoomMeetingLis
{
    public function handle(UpdateZoomMeeting $event)
    {
        if (Module_is_active('ActivityLog')) {
            $meeting = $event->meeting;

            $activity = new AllActivityLog();
            $activity['module'] = 'ZoomMeeting';
            $activity['sub_module'] = 'Zoom Meeting';
            $activity['description'] = __('Zoom Meeting updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $meeting->created_by;
            $activity->save();
        }
    }
}
