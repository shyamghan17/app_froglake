<?php

namespace Workdo\ActivityLog\Listeners;

use App\Events\CreateUser;
use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;

class CreateUserLis
{
    public function handle(CreateUser $event)
    {
        if (Module_is_active('ActivityLog')) {
            $user = $event->user;

            $activity = new AllActivityLog();
            $activity['module'] = 'User Management';
            $activity['sub_module'] = 'User';
            $activity['description'] = __('New User created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $user->created_by;
            $activity->save();
        }
    }
}
