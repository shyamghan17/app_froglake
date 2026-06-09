<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\InsuranceManagement\Events\CreatePolicy;

class CreatePolicyLis
{
    public function handle(CreatePolicy $event)
    {
        if (Module_is_active('ActivityLog')) {
            $policy = $event->policy;

            $activity = new AllActivityLog();
            $activity['module'] = 'InnovationCenter';
            $activity['sub_module'] = 'Policy';
            $activity['description'] = __('Policy created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $policy->created_by;
            $activity->save();
        }
    }
}
