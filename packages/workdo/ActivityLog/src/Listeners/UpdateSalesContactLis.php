<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\Events\UpdateSalesContact;

class UpdateSalesContactLis
{
    public function handle(UpdateSalesContact $event)
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
