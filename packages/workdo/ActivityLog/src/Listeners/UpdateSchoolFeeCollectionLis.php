<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\UpdateFeeCollection;

class UpdateSchoolFeeCollectionLis
{
    public function handle(UpdateFeeCollection $event)
    {
        if (Module_is_active('ActivityLog')) {
            $feeCollection = $event->feeCollection;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Fee Collection';
            $activity['description'] = __('School Fee Collection updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $feeCollection->created_by;
            $activity->save();
        }
    }
}