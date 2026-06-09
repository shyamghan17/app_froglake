<?php

namespace Workdo\BeautySpaManagement\Listeners;

use App\Events\DefaultData;
use Workdo\BeautySpaManagement\Models\BeautyCustomPage;
use Workdo\BeautySpaManagement\Models\BeautyUtility;

class DataDefault
{
    public function __construct()
    {
        //
    }

    public function handle(DefaultData $event)
    {
        $company_id = $event->company_id;
        $user_module = $event->user_module ? explode(',', $event->user_module) : [];
        if(!empty($user_module))
        {
            if (in_array("BeautySpaManagement", $user_module))
            {
                BeautyUtility::defaultdata($company_id);
            }
        }
    }
}