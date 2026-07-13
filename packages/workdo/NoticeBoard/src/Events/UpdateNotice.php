<?php

namespace Workdo\NoticeBoard\Events;

use Workdo\NoticeBoard\Models\Notice;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class UpdateNotice
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public Notice $notice
    ) {
    }
}
