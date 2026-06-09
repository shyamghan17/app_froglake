<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\VisitorManagement\Events\CreateVisitPurpose;
use Workdo\SMS\Services\SendSMS;

class CreateVisitPurposeLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateVisitPurpose $event)
    {
        $visitPurpose = $event->visitpurpose;
        if (Module_is_active('SMS') && company_setting('SMS New Visit Purposes') == 'on') {


            if ($visitPurpose->creator_id == $visitPurpose->created_by) {
                $user = User::find($visitPurpose->created_by) ??  null;
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'company_name' => $user->name ?? '',
                        'name' => $visitPurpose->name ??  '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Visit Purposes', $user->mobile_no);
                }
            }
        }
    }
}
