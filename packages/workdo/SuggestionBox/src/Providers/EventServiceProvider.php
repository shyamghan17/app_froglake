<?php

namespace Workdo\SuggestionBox\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\GivePermissionToRole;
use Workdo\SuggestionBox\Listeners\GiveRoleToPermission;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        GivePermissionToRole::class => [
            GiveRoleToPermission::class,
        ],
    ];
}
