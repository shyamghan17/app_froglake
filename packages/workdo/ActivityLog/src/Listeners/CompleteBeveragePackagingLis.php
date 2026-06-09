<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\BeverageManagement\Events\CompleteBeveragePackaging;

class CompleteBeveragePackagingLis
{
    public function handle(CompleteBeveragePackaging $event)
    {
        if (Module_is_active('ActivityLog')) {
            $packaging = $event->beveragePackaging;

            $activity = new AllActivityLog();
            $activity['module'] = 'BeverageManagement';
            $activity['sub_module'] = 'Packaging';
            $activity['description'] = __('Beverage Packaging completed by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $packaging->created_by;
            $activity->save();
        }
    }
}