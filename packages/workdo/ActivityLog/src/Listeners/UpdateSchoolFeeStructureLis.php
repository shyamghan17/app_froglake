<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\UpdateFeeStructure;

class UpdateSchoolFeeStructureLis
{
    public function handle(UpdateFeeStructure $event)
    {
        if (Module_is_active('ActivityLog')) {
            $feeStructure = $event->feeStructure;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Fee Structure';
            $activity['description'] = __('School Fee Structure updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $feeStructure->created_by;
            $activity->save();
        }
    }
}