<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Account\Events\CreateCustomer;
use Workdo\SMS\Services\SendSMS;

class CreateCustomerLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateCustomer $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Customer') == 'on' && isset($event->customer->user_id)) {
            $user = User::find($event->customer->user_id);
            $uArr = [
                'company_name' => $user->name,
            ];

            SendSMS::SendMsgs($uArr, 'New Customer', $user->mobile_no);
        }
    }
}
