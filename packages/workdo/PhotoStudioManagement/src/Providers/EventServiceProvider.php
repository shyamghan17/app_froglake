<?php

namespace Workdo\PhotoStudioManagement\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\DefaultData;
use App\Events\GivePermissionToRole;
use Workdo\PhotoStudioManagement\Listeners\DataDefault;
use Workdo\PhotoStudioManagement\Listeners\GiveRoleToPermission;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DefaultData::class => [
            DataDefault::class,
        ],
        GivePermissionToRole::class => [
            GiveRoleToPermission::class,
        ],
    ];
}