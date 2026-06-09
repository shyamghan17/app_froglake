<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Training\Events\CreateTrainer;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateTrainerLis
{
    public function handle(CreateTrainer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $trainer = $event->trainer;

            $activity = new AllActivityLog();
            $activity['module'] = 'Training';
            $activity['sub_module'] = 'Trainer';
            $activity['description'] = __('New Trainer created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $trainer->created_by;
            $activity->save();
        }
    }
}