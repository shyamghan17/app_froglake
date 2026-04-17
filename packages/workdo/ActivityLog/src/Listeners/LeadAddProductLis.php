<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class LeadAddProductLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $leadProduct = $event->lead;

            $activity = new AllActivityLog();
            $activity['module'] = 'Lead';
            $activity['sub_module'] = 'Lead';
            $activity['description'] = __('New Product Add in lead ') . $leadProduct->name . __(' by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $leadProduct->created_by;
            $activity->save();
        }
    }
}