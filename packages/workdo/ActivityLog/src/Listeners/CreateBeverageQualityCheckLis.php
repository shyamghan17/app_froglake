<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\Events\CreateBeverageQualityCheck;

class CreateBeverageQualityCheckLis
{
    public function handle(CreateBeverageQualityCheck $event)
    {
        if (Module_is_active('ActivityLog')) {
            $qualityCheck = $event->beverageQualityCheck;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeverageManagement';
            $activity['sub_module'] = 'Quality Check';
            $activity['description'] = __('Beverage Quality Check created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $qualityCheck->created_by;
            $activity->save();
        }
    }
}