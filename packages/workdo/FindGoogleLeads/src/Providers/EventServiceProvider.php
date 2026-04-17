<?php

namespace Workdo\FindGoogleLeads\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Workdo\FindGoogleLeads\Listeners\CreateLeadLis;
use Workdo\Lead\Events\CreateLead;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CreateLead::class => [
            CreateLeadLis::class
        ],
    ];
}