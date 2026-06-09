<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\FormBuilder\Events\CreateForm;
use Workdo\SMS\Services\SendSMS;

class CreateFormLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateForm $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Form') == 'on') {
            $form = $event->form;
            if ($form->creator_id != $form->created_by) {
                $user = User::find($form->created_by) ??  null;
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'name' => $form->name ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Form', $user->mobile_no, $form->created_by);
                }
            }
        }
    }
}
