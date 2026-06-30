<?php

namespace Workdo\Account\Listeners;

use App\Events\CreateUser;
use Workdo\Account\Services\UserAccountPartySyncService;

class SyncAccountPartyFromUserListener
{
    public function __construct(private readonly UserAccountPartySyncService $syncService)
    {
    }

    public function handle(CreateUser $event): void
    {
        $this->syncService->syncForUser($event->user);
    }
}
