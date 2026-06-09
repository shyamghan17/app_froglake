<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\ToDo\Events\CompleteToDo;
use Workdo\SMS\Services\SendSMS;

class CompleteToDoLis
{
    public function handle(CompleteToDo $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Complete To Do') == 'on') {
            $todo = $event->todo;
            
            if ($todo->created_by) {
                $user = User::find($todo->created_by);
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'user_name' => $user->name ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'Complete To Do', $user->mobile_no, $todo->created_by);
                }
            }
        }
    }
}