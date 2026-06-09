<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\ToDo\Events\CreateToDo;
use Workdo\SMS\Services\SendSMS;

class CreateToDoLis
{
    public function handle(CreateToDo $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New To Do') == 'on') {
            $todo = $event->todo;
            $users = User::whereIn('id',  $todo->assigned_to ?? [])->get();
            foreach ($users as $user) {
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'name' => $todo->title ?? '',
                        'module_name' => $todo->module ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New To Do', $user->mobile_no, $todo->created_by);
                }
            }
        }
    }
}
