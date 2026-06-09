<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\CMMS\Events\CreateSupplier;
use Workdo\SMS\Services\SendSMS;

class CreateSupplierLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateSupplier $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Supplier') == 'on') {
            $uArr = [
                'company_name' => User::find($event->supplier->created_by)->name ?? '-',
                'user_name' => $event->supplier->name ?? '-',
            ];

            SendSMS::SendMsgs($uArr, 'New Supplier', $event->supplier->contact ?? null);
        }
    }
}
