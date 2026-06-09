<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Training\Events\UpdateTrainer;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateTrainerLis
{
    public function handle(UpdateTrainer $event)
    {
        if (Module_is_active('ActivityLog')) {
            $trainer = $event->trainer;

            $activity = new AllActivityLog();
            $activity['module'] = 'Training';
            $activity['sub_module'] = 'Trainer';
            $activity['description'] = __('Trainer updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $trainer->created_by;
            $activity->save();
        }
    }
}