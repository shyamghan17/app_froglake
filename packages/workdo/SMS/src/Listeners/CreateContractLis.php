<?php

namespace Workdo\SMS\Listeners;

use Workdo\Contract\Events\CreateContract;
use Workdo\SMS\Services\SendSMS;

class CreateContractLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateContract $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Contract') == 'on') {
            $uArr = [
                'contract_number' => $event->contract->contract_number ?? '-',
            ];

            SendSMS::SendMsgs($uArr, 'New Contract', $event->contract->user->mobile_no ?? null, $event->contract->created_by);
        }
    }
}
