<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Pos\Events\CreatePos;
use Workdo\SMS\Services\SendSMS;

class CreatePosLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreatePos $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Purchase') == 'on') {
            $user = User::find($event->posSale->customer_id);
            $uArr = [
                'purchase_id' => $event->posSale->sale_number ?? 'Unknown',
                'company_name' => $user->name,
            ];
            SendSMS::SendMsgs($uArr, 'New Purchase', $user->mobile_no);
        }
    }
}