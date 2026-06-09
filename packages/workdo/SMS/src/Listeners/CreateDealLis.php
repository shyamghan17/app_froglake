<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Lead\Events\CreateDeal;
use Workdo\SMS\Services\SendSMS;

class CreateDealLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateDeal $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Deal') == 'on') {
            $assign_users = User::whereIn('id', $event->request->clients)->get();
            foreach ($assign_users as $user) {
                if (!empty($user->mobile_no)) {
                    $uArr = [
                        'company_name' => $user->name,
                    ];
                    SendSMS::SendMsgs($uArr, 'New Deal', $user->mobile_no);
                }
            }
        }
    }
}
