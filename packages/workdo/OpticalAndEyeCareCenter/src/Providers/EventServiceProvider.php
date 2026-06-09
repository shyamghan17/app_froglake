<?php

namespace Workdo\OpticalAndEyeCareCenter\Providers;

use App\Events\DefaultData;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Workdo\OpticalAndEyeCareCenter\Listeners\DataDefault;

class EventServiceProvider extends ServiceProvider
{
     protected $listen = [
        DefaultData::class => [
            DataDefault::class,
        ],
    ];
}
