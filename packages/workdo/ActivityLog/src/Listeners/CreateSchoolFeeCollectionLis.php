<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\School\Events\CreateFeeCollection;

class CreateSchoolFeeCollectionLis
{
    public function handle(CreateFeeCollection $event)
    {
        if (Module_is_active('ActivityLog')) {
            $feeCollection = $event->feeCollection;

            $activity = new AllActivityLog();
            $activity['module'] = 'School';
            $activity['sub_module'] = 'Fee Collection';
            $activity['description'] = __('New School Fee Collection created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $feeCollection->created_by;
            $activity->save();
        }
    }
}