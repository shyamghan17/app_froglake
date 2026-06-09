<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\Events\CreateCollectionCenter;

class CreateCollectionCenterLis
{
    public function handle(CreateCollectionCenter $event)
    {
        if (Module_is_active('ActivityLog')) {
            $collectionCenter = $event->collectionCenter;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeverageManagement';
            $activity['sub_module'] = 'Collection Center';
            $activity['description'] = __('Collection Center created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $collectionCenter->created_by;
            $activity->save();
        }
    }
}