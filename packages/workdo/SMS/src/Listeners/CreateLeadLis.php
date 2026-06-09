<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Termwind\Components\Dd;
use Workdo\Lead\Events\CreateLead;
use Workdo\SMS\Services\SendSMS;

class CreateLeadLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateLead $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Lead') == 'on') {
            $user = User::find($event->lead->user_id);
            $uArr = [
                'company_name' => $user->name,
            ];

            SendSMS::SendMsgs($uArr, 'New Lead', $user->mobile_no);
        }
    }
}
