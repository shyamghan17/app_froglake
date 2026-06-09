<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\VisitorManagement\Events\CreateVisitor;
use Workdo\SMS\Services\SendSMS;

class CreateVisitorLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateVisitor $event)
    {
        $visitor = $event->visitor;
        if (Module_is_active('SMS') && company_setting('SMS New Visitor') == 'on') {
            $mobile = $visitor->phone ?? null;
            if ($mobile) {
                $uArr = [
                    'company_name' => User::find($visitor->created_by)->name ?? '',
                    'name' => $visitor->name ?? '',
                ];
                SendSMS::SendMsgs($uArr, 'New Visitor', $mobile);
            }
        }
    }
}
