<?php

namespace Workdo\SuggestionBox\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuggestionUtility extends Model
{
    public static function GivePermissionToRoles($role_id = null, $rolename = null)
    {
        $staff_permission = [
            'manage-suggestion-box',
            'manage-suggestions',
            'manage-any-suggestions',
            'manage-own-suggestions',
            'view-suggestions',
            'create-suggestions',
            'edit-suggestions',
            'delete-suggestions',
            'vote-suggestions',
        ];

        if ($rolename == 'staff') {
            $roles_v = Role::where('name', 'staff')->where('id', $role_id)->first();
            foreach ($staff_permission as $permission_v) {
                $permission = Permission::where('name', $permission_v)->first();
                if (!empty($permission)) {
                    if (!$roles_v->hasPermissionTo($permission_v)) {
                        $roles_v->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
