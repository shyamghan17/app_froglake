<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Holidayz\Events\UpdateHolidayzRoom;

class UpdateHolidayzRoomLis
{
    public function handle(UpdateHolidayzRoom $event)
    {
        if (Module_is_active('ActivityLog')) {
            $room = $event->room;

            $activity = new AllActivityLog();
            $activity['module'] = 'Holidayz';
            $activity['sub_module'] = 'Room';
            $activity['description'] = __('Hotel Room updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $room->created_by;
            $activity->save();
        }
    }
}