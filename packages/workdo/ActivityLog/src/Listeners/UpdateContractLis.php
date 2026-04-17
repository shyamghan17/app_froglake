<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class UpdateContractLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $contract = $event->contract;

            $activity = new AllActivityLog();
            $activity['module'] = 'Contract';
            $activity['sub_module'] = 'Contract';
            $activity['description'] = __('Contract updated by the ');                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $contract->created_by;
            $activity->save();
        }
    }
}