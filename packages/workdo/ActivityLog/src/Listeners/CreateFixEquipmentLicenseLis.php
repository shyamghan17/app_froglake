<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateFixEquipmentLicenseLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $fixEquipmentLicense = $event->fixEquipmentLicense;

            $activity = new AllActivityLog();
            $activity['module'] = 'FixEquipment';
            $activity['sub_module'] = 'License';
            $activity['description'] = __('Fix Equipment License created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $fixEquipmentLicense->created_by;
            $activity->save();
        }
    }
}