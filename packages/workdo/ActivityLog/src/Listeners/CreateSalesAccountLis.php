<?php

namespace Workdo\ActivityLog\Listeners;

use App\Models\User;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateSalesAccountLis
{
    public function handle($event)
    {
        if (Module_is_active('ActivityLog')) {
            $salesAccount = $event->account;
            $user = User::find($salesAccount->user_id);

            $activity = new AllActivityLog();
            $activity['module'] = 'Sales';
            $activity['sub_module'] = 'Account';
            if (isset($user->name)) {
                $activity['description']    = __('New Account Created for ') . $user->name . __(' by the ');
            } else {
                $activity['description']    = __('New Account Created by the ');
            }                          
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $salesAccount->created_by;
            $activity->save();
        }
    }
}