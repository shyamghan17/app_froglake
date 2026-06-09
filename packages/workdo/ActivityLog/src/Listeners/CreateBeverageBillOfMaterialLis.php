<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\Events\CreateBeverageBillOfMaterial;

class CreateBeverageBillOfMaterialLis
{
    public function handle(CreateBeverageBillOfMaterial $event)
    {
        if (Module_is_active('ActivityLog')) {
            $billOfMaterial = $event->beverageBillOfMaterial;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeverageManagement';
            $activity['sub_module'] = 'Bill of Material';
            $activity['description'] = __('Beverage Bill of Material created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $billOfMaterial->created_by;
            $activity->save();
        }
    }
}