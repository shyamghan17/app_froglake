<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\Events\UpdateBeverageQualityCheck;

class UpdateBeverageQualityCheckLis
{
    public function handle(UpdateBeverageQualityCheck $event)
    {
        if (Module_is_active('ActivityLog')) {
            $qualityCheck = $event->beverageQualityCheck;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeverageManagement';
            $activity['sub_module'] = 'Quality Check';
            $activity['description'] = __('Beverage Quality Check updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $qualityCheck->created_by;
            $activity->save();
        }
    }
}