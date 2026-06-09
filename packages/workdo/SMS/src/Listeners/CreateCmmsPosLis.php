<?php

namespace Workdo\SMS\Listeners;

use Workdo\CMMS\Events\CreateCmmsPos;
use Workdo\SMS\Services\SendSMS;

class CreateCmmsPosLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateCmmsPos $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New POs') == 'on') {
            $uArr = [
                'company_name' => $event->pos->created_by_name ?? '-',
                'user_name' => $event->pos->user->name ?? '',
                'pos_number' => $event->pos->pos_number ?? '',
                'supplier_name' => $event->pos->supplier->name ?? '',
            ];

            SendSMS::SendMsgs($uArr, 'New POs', $event->pos->user->mobile_no ?? null);
        }
    }
}
