<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Account\Events\CreateVendor;
use Workdo\SMS\Services\SendSMS;

class CreateVendorLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateVendor $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Vendor') == 'on' && isset($event->vendor->user_id)) {
            $user = User::find($event->vendor->user_id);
            $uArr = [
                'company_name' => $user->name,
            ];

            SendSMS::SendMsgs($uArr, 'New Vendor', $user->mobile_no);
        }
    }
}
