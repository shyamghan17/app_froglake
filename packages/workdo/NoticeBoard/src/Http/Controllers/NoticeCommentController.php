<?php

namespace Workdo\NoticeBoard\Http\Controllers;

use Workdo\NoticeBoard\Models\Notice;
use Workdo\NoticeBoard\Models\NoticeComment;

use Workdo\NoticeBoard\Http\Requests\StoreNoticeCommentRequest;

use Workdo\NoticeBoard\Events\CreateNoticeComment;
use Workdo\NoticeBoard\Events\CreateNoticeCommentReply;
use Workdo\NoticeBoard\Events\DestroyNoticeComment;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class NoticeCommentController extends Controller
{
    public function store(StoreNoticeCommentRequest $request, Notice $notice)
    {
        if ($notice->allow_comments && $notice->creator_id !== Auth::id()) {
            $noticeComment             = new NoticeComment();
            $noticeComment->notice_id  = $notice->id;
            $noticeComment->user_id    = Auth::id();
            $noticeComment->parent_id  = null;
            $noticeComment->comment    = $request->comment;
            $noticeComment->creator_id = Auth::id();
            $noticeComment->created_by = creatorId();
            $noticeComment->save();

            CreateNoticeComment::dispatch($request, $noticeComment);

            return back()->with('success', __('The comment has been posted successfully.'));
        } else {
            return back()->with('error', __('Comments are disabled for this notice.'));
        }
    }

    public function reply(StoreNoticeCommentRequest $request, Notice $notice, NoticeComment $comment)
    {
        if (Auth::user()->can('reply-notices-comments')) {
            if (!$notice->allow_comments) {
                return back()->with('error', __('Comments are disabled for this notice.'));
            }

            $noticeComment             = new NoticeComment();
            $noticeComment->notice_id  = $notice->id;
            $noticeComment->user_id    = Auth::id();
            $noticeComment->parent_id  = $comment->id;
            $noticeComment->comment    = $request->comment;
            $noticeComment->creator_id = Auth::id();
            $noticeComment->created_by = creatorId();
            $noticeComment->save();

            CreateNoticeCommentReply::dispatch($request, $noticeComment);

            return back()->with('success', __('The reply has been posted successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(Notice $notice, NoticeComment $comment)
    {
        if (Auth::user()->can('delete-any-notices-comments') || (Auth::user()->can('delete-own-notices-comments') && $comment->creator_id === Auth::id())) {
            DestroyNoticeComment::dispatch($comment);

            $comment->delete();

            return back()->with('success', __('The comment has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}
