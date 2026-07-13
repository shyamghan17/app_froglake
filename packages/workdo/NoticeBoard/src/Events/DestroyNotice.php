<?php

namespace Workdo\NoticeBoard\Events;

use Workdo\NoticeBoard\Models\Notice;
use Illuminate\Foundation\Events\Dispatchable;

class DestroyNotice
{
    use Dispatchable;

    public function __construct(
        public Notice $notice
    ) {
    }
}
