<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Holidayz\Events\CreateHolidayzRoom;

class CreateHolidayzRoomLis
{
    public function handle(CreateHolidayzRoom $event)
    {
        if (Module_is_active('ActivityLog')) {
            $room = $event->room;

            $activity = new AllActivityLog();
            $activity['module'] = 'Holidayz';
            $activity['sub_module'] = 'Room';
            $activity['description'] = __('New Hotel Room created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $room->created_by;
            $activity->save();
        }
    }
}