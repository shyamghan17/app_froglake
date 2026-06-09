<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\Announcement;

class DestroyAnnouncement
{
    use Dispatchable, SerializesModels;

    public function __construct(
          public Announcement $announcement
    )
    {
        //
    }
}