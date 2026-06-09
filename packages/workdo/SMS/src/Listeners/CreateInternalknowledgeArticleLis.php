<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Internalknowledge\Events\CreateInternalknowledgeArticle;
use Workdo\Internalknowledge\Models\InternalknowledgeBook;
use Workdo\SMS\Services\SendSMS;

class CreateInternalknowledgeArticleLis
{
    public function handle(CreateInternalknowledgeArticle $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Article') == 'on') {
            $article = $event->internalknowledgeArticle;

            if ($article->created_by == $article->creator_id) {
                $user = User::find($article->created_by);
                $book = InternalknowledgeBook::find($article->internalknowledge_book_id);
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'article_type' => $article->type ?? '',
                        'book_name' => $book->title ?? '',
                        'company_name' => $user->name ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Article', $user->mobile_no, $article->created_by);
                }
            }
        }
    }
}
