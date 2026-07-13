<?php

namespace Workdo\NoticeBoard\Events;

use Workdo\NoticeBoard\Models\NoticeComment;
use Illuminate\Foundation\Events\Dispatchable;

class DestroyNoticeComment
{
    use Dispatchable;

    public function __construct(
        public NoticeComment $noticeComment
    ) {
    }
}
