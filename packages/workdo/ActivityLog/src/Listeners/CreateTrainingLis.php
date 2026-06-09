<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Training\Events\CreateTraining;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateTrainingLis
{
    public function handle(CreateTraining $event)
    {
        if (Module_is_active('ActivityLog')) {
            $training = $event->training;

            $activity = new AllActivityLog();
            $activity['module'] = 'Training';
            $activity['sub_module'] = 'Training';
            $activity['description'] = __('New Training created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $training->created_by;
            $activity->save();
        }
    }
}