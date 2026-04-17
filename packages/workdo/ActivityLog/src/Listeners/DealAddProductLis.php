<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class DealAddProductLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $dealProduct = $event->deal;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Deal';
            $activity['description'] = __('New Product Add in deal ') . $dealProduct->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $dealProduct->created_by;
            $activity->save();
        }
    }
}