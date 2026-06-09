<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\ProductService\Events\CreateProductServiceItem;
use Workdo\SMS\Services\SendSMS;

class CreateProductServiceLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateProductServiceItem $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New ProductService') == 'on' && isset($event->vendor->user_id)) {
            $user = User::find($event->item->creator_id ??  null);
            $uArr = [
                'user_name' => $user->name,
            ];

            SendSMS::SendMsgs($uArr, 'New ProductService', $user->mobile_no);
        }
    }
}
