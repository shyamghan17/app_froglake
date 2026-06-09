<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\GoogleMeet\Events\CreateGoogleMeeting;

class CreateGoogleMeetingLis
{
    public function handle(CreateGoogleMeeting $event)
    {
        if (Module_is_active('ActivityLog')) {
            $meeting = $event->meeting;

            $activity = new AllActivityLog();
            $activity['module'] = 'GoogleMeet';
            $activity['sub_module'] = 'Google Meeting';
            $activity['description'] = __('New Google Meeting created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $meeting->created_by;
            $activity->save();
        }
    }
}
