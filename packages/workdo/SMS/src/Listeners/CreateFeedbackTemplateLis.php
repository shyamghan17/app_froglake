<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\Feedback\Events\CreateTemplate;
use Workdo\SMS\Services\SendSMS;

class CreateFeedbackTemplateLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateTemplate $event)
    {
        $template = $event->template;
        if (Module_is_active('SMS') && company_setting('SMS New Template') == 'on') {
            if ($template->creator_id != $template->created_by) {
                $user = User::find($template->created_by) ??  null;
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'company_name' => $user->name ?? '',
                        'module_name' => $template->moduleName->module ?? 'Module',
                        'submodule_name' => $template->moduleName->submodule ?? 'Submodule',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Template', $user->mobile_no);
                }
            }
        }
    }
}
