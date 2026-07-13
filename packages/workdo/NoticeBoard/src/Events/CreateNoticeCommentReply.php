<?php

namespace Workdo\NoticeBoard\Events;

use Workdo\NoticeBoard\Models\NoticeComment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

class CreateNoticeCommentReply
{
    use Dispatchable;

    public function __construct(
        public Request $request,
        public NoticeComment $noticeComment
    ) {
    }
}
