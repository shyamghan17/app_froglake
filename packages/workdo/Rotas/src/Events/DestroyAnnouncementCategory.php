<?php

namespace Workdo\Rotas\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Workdo\Rotas\Models\AnnouncementCategory;

class DestroyAnnouncementCategory
{
    use Dispatchable, SerializesModels;

    public function __construct(
          public AnnouncementCategory $announcementCategory
    )
    {
        //
    }
}