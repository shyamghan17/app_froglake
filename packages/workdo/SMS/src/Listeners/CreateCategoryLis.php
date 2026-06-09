<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\InnovationCenter\Events\CreateCategory;
use Workdo\SMS\Services\SendSMS;

class CreateCategoryLis
{
    public function handle(CreateCategory $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Category') == 'on') {
            $category = $event->category;
            if ($category->created_by != $category->creator_id) {
                $user = User::find($category->created_by);
                if ($user && $user->mobile_no) {
                    $uArr = [
                        'name' => $category->name ?? '',
                        'company_name' => $user->name ?? '',
                    ];
                    SendSMS::SendMsgs($uArr, 'New Category', $user->mobile_no, $category->created_by);
                }
            }
        }
    }
}
