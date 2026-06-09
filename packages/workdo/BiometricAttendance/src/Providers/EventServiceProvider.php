<?php

namespace Workdo\BiometricAttendance\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Workdo\BiometricAttendance\Listeners\SaveBiometricFieldValues;
use Workdo\Hrm\Events\CreateEmployee;
use Workdo\Hrm\Events\UpdateEmployee;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CreateEmployee::class => [
            SaveBiometricFieldValues::class,
        ],
        UpdateEmployee::class => [
            SaveBiometricFieldValues::class,
        ],
    ];
}