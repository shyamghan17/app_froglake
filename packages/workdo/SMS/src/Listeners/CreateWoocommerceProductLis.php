<?php

namespace Workdo\SMS\Listeners;

use App\Models\User;
use Workdo\WordpressWoocommerce\Events\CreateWoocommerceProduct;
use Workdo\SMS\Services\SendSMS;

class CreateWoocommerceProductLis
{
    public function __construct()
    {
        //
    }

    public function handle(CreateWoocommerceProduct $event)
    {
        if (Module_is_active('SMS') && company_setting('SMS New Product') == 'on') {
            $user = creatorUser();
            if ($user && $user->mobile_no) {
                $uArr = [
                    'company_name' => $user->name ?? '',
                    'name' => $event->wooProduct['name'] ?? 'Product',
                ];
                SendSMS::SendMsgs($uArr, 'New Product', $user->mobile_no);
            }
        }
    }
}
