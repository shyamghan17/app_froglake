<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateSalesAccountLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesAccount = $event->account;

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Account';
            $activity['description'] = __('Sales Account updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesAccount->created_by;
            $activity->save();
        }
    }
}