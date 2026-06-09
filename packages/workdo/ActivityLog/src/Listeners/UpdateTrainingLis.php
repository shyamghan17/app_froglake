<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\Training\Events\UpdateTraining;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateTrainingLis
{
    public function handle(UpdateTraining $event)
    {
        if (Module_is_active('ActivityLog')) {
            $training = $event->training;

            $activity = new AllActivityLog();
            $activity['module'] = 'Training';
            $activity['sub_module'] = 'Training';
            $activity['description'] = __('Training updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $training->created_by;
            $activity->save();
        }
    }
}