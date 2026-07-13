<?php

namespace Workdo\NoticeBoard\Providers;

use App\Events\GivePermissionToRole;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Workdo\NoticeBoard\Listeners\GiveRoleToPermission;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        GivePermissionToRole::class => [
            GiveRoleToPermission::class,
        ],
    ];
}
