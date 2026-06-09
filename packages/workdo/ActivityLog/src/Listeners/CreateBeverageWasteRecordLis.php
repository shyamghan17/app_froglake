<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\Events\CreateBeverageWasteRecord;

class CreateBeverageWasteRecordLis
{
    public function handle(CreateBeverageWasteRecord $event)
    {
        if (Module_is_active('ActivityLog')) {
            $wasteRecord = $event->beverageWasteRecord;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeverageManagement';
            $activity['sub_module'] = 'Waste Record';
            $activity['description'] = __('Beverage Waste Record created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $wasteRecord->created_by;
            $activity->save();
        }
    }
}