<?php

namespace Workdo\SMS\Listeners;

use App\Events\CreateUser;
use Workdo\SMS\Services\SendSMS;

class CreateUserLis
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CreateUser $event)
    {
        $user = $event->user;
        if (Module_is_active('SMS') && company_setting('SMS New User') == 'on') {
            $uArr = [
                'user_name' => $user->name,
            ];

            SendSMS::SendMsgs($uArr, 'New User', $user->mobile_no);
        }
    }
}
