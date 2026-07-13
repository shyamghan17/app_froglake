<?php

namespace Workdo\NoticeBoard\Listeners;

use App\Events\GivePermissionToRole;
use Workdo\NoticeBoard\Models\Notice;

class GiveRoleToPermission
{
    public function handle(GivePermissionToRole $event)
    {
        $role_id     = $event->role_id;
        $rolename    = $event->rolename;
        $user_module = $event->user_module ? explode(',', $event->user_module) : [];

        if (!empty($user_module)) {
            if (in_array('NoticeBoard', $user_module)) {
                Notice::GivePermissionToRoles($role_id, $rolename);
            }
        }
    }
}
