<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Rotas\Events\CreateShift;

class CreateRotasShiftLis
{
    public function handle(CreateShift $event)
    {
        if (Module_is_active('ActivityLog')) {
            $shift = $event->shift;

            $activity = new AllActivityLog();
            $activity['module'] = 'Rotas';
            $activity['sub_module'] = 'Shift';
            $activity['description'] = __('New Work Shift created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $shift->created_by;
            $activity->save();
        }
    }
}