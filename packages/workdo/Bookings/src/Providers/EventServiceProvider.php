<?php

namespace Workdo\Bookings\Providers;

use App\Events\DefaultData;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Workdo\Bookings\Listeners\DataDefault;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        DefaultData::class => [
           DataDefault::class,
        ],
    ];
}