<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateFixEquipmentAssetLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $fixEquipmentAsset = $event->fixEquipmentAsset;

            $activity = new AllActivityLog();
            $activity['module'] = 'FixEquipment';
            $activity['sub_module'] = 'Asset';
            $activity['description'] = __('Fix Equipment Asset updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $fixEquipmentAsset->created_by;
            $activity->save();
        }
    }
}