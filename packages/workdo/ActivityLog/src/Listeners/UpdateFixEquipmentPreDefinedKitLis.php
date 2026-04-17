<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateFixEquipmentPreDefinedKitLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $fixEquipmentPreDefinedKit = $event->fixEquipmentPreDefinedKit;

            $activity = new AllActivityLog();
            $activity['module'] = 'FixEquipment';
            $activity['sub_module'] = 'PreDefinedKit';
            $activity['description'] = __('Fix Equipment PreDefined Kit updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $fixEquipmentPreDefinedKit->created_by;
            $activity->save();
        }
    }
}