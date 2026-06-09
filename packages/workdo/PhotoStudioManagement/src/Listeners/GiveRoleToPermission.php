<?php

namespace Workdo\PhotoStudioManagement\Listeners;

use App\Events\GivePermissionToRole;
use Workdo\PhotoStudioManagement\Models\PhotoStudioManagementUtility;

class GiveRoleToPermission
{
    public function handle(GivePermissionToRole $event)
    {
        $role_id  = $event->role_id;
        $rolename = $event->rolename;

        $user_module = $event->user_module ? explode(',', $event->user_module) : [];

        if (!empty($user_module) && in_array('PhotoStudioManagement', $user_module)) {
            PhotoStudioManagementUtility::GivePermissionToRoles($role_id, $rolename);
        }
    }
}
