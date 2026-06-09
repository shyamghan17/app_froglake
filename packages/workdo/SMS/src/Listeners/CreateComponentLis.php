<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\CMMS\Events\CreateComponent;
use Workdo\SMS\Services\SendSMS;

class CreateComponentLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateComponent $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Component') == 'on') {
            $uArr = [
                'component_name' => $event->component->name ?? '-',
            ];

            SendSMS::SendMsgs($uArr, 'New Component', User::find($event->component->created_by)->mobile_no ?? null);
        }
    }
}
