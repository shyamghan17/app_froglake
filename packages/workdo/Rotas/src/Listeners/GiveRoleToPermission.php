<?php

namespace Workdo\Rotas\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\GivePermissionToRole;
 use Workdo\Rotas\Models\Rota;


class GiveRoleToPermission
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle($event)
    {
        $role_id = $event->role_id;
        $rolename = $event->rolename;
        $user_module = $event->user_module ? explode(',', $event->user_module) : [];
        if (!empty($user_module)) {
            if (in_array("Rotas", $user_module)) {
                Rota::GivePermissionToRoles($role_id, $rolename);
            }
        }
    }
}