<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Contract\Events\UpdateContract;

class UpdateContractLis
{
    public function handle(UpdateContract $event)
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
