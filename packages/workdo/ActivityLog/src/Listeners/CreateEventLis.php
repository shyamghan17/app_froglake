<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\EventsManagement\Events\CreateEvent;

class CreateEventLis
{
    public function handle(CreateEvent $event)
    {
        if (Module_is_active('ActivityLog')) {
            $eventModel = $event->event;

            $activity = new AllActivityLog();
            $activity['module'] = 'EventsManagement';
            $activity['sub_module'] = 'Event';
            $activity['description'] = __('Event created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $eventModel->created_by;
            $activity->save();
        }
    }
}
