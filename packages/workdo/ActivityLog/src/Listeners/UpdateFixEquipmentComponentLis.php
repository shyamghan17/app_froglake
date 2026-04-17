<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateFixEquipmentComponentLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $fixEquipmentComponent = $event->fixEquipmentComponent;

            $activity = new AllActivityLog();
            $activity['module'] = 'FixEquipment';
            $activity['sub_module'] = 'Component';
            $activity['description'] = __('Fix Equipment Component updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $fixEquipmentComponent->created_by;
            $activity->save();
        }
    }
}