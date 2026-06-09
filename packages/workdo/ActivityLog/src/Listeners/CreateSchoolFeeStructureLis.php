<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\CreateFeeStructure;

class CreateSchoolFeeStructureLis
{
    public function handle(CreateFeeStructure $event)
    {
        if (Module_is_active('ActivityLog')) {
            $feeStructure = $event->feeStructure;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Fee Structure';
            $activity['description'] = __('New School Fee Structure created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $feeStructure->created_by;
            $activity->save();
        }
    }
}