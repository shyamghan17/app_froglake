<?php

namespace Workdo\SuggestionBox\Listeners;

use Workdo\SuggestionBox\Models\SuggestionUtility;

class GiveRoleToPermission
{
    public function handle($event)
    {
        $role_id     = $event->role_id;
        $rolename    = $event->rolename;
        $user_module = $event->user_module ? explode(',', $event->user_module) : [];
        if (!empty($user_module)) {
            if (in_array("SuggestionBox", $user_module)) {
                SuggestionUtility::GivePermissionToRoles($role_id, $rolename);
            }
        }
    }
}