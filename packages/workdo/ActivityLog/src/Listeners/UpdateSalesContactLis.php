<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateSalesContactLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesContact = $event->contact;

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Contact';
            $activity['description'] = __('Sales Contact updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesContact->created_by;
            $activity->save();
        }
    }
}