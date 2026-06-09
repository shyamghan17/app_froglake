<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Recruitment\Events\ConvertOfferToEmployee;
use Workdo\SMS\Services\SendSMS;

class ConvertOfferToEmployeeLis
{
    public function __construct()
    {
        //
    }

    public function handle(ConvertOfferToEmployee $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Convert To Employee') == 'on') {
            $user = User::find($event->employee->created_by);
            if (isset($user->mobile_no)) {
                $uArr = [
                    'company_name' => $user->name ?? '-',
                ];
                SendSMS::SendMsgs($uArr, 'Convert To Employee', $user->mobile_no ?? null, $user->id);
            }
        }
    }
}
