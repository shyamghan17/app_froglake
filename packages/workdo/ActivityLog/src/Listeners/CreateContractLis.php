<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateContractLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $contract = $event->contract;

            $activity = new AllActivityLog();
            $activity['module'] = 'Contract';
            $activity['sub_module'] = 'Contract';
            $activity['description'] = __('New Contract created by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $contract->created_by;
            $activity->save();
        }
    }
}