<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\FormBuilder\Events\FormConverted;
use Workdo\SMS\Services\SendSMS;

class FormConvertedLis
{
    public function __construct()
    {
        //
    }

    public function handle(FormConverted $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS Convert To Modal') == 'on') {
            $form = $event->form;
            if ($form->creator_id == $form->created_by) {
                $user = User::find($form->created_by) ?? null;
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'company_name' => $user->name ?? ''
                    ];
                    SendSMS::SendMsgs($uArr, 'Convert To Modal', $user->mobile_no, $form->created_by);
                }
            }
        }
    }
}
