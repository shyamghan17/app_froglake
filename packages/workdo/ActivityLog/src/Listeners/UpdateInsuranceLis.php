<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateInsuranceLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $insurance = $event->insurance;

            $activity = new AllActivityLog();
            $activity['module'] = 'InnovationCenter';
            $activity['sub_module'] = 'Insurance';
            $activity['description'] = __('Insurance updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $insurance->created_by;
            $activity->save();
        }
    }
}