<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Internalknowledge\Events\CreateInternalknowledgeBook;
use Workdo\SMS\Services\SendSMS;

class CreateInternalknowledgeBookLis
{
    public function handle(CreateInternalknowledgeBook $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Book') == 'on') {
            $book = $event->internalknowledgeBook;

            if ($book->created_by == $book->creator_id) {
                $user = User::find($book->created_by);
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'name' => $book->title ?? '',
                        'user_name' => $user->name ?? '',
                        'company_name' => $user->name ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Book', $user->mobile_no, $book->created_by);
                }
            }
        }
    }
}
