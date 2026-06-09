<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\Events\CreateBeverageRawMaterial;

class CreateBeverageRawMaterialLis
{
    public function handle(CreateBeverageRawMaterial $event)
    {
        if (Module_is_active('ActivityLog')) {
            $rawMaterial = $event->beverageRawMaterial;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeverageManagement';
            $activity['sub_module'] = 'Raw Material';
            $activity['description'] = __('Beverage Raw Material created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $rawMaterial->created_by;
            $activity->save();
        }
    }
}