<?php

namespace Workdo\OpticalAndEyeCareCenter\Listeners;

use App\Events\DefaultData;
use Workdo\OpticalAndEyeCareCenter\Helpers\OpticalUtility;

class DataDefault
{
    public function handle(DefaultData $event)
    {
        $company_id = $event->company_id;
        $user_module = $event->user_module ? explode(',', $event->user_module) : [];
        if(!empty($user_module))
        {
            if (in_array("OpticalAndEyeCareCenter", $user_module))
            {
                OpticalUtility::defaultdata($company_id);
            }
        }
    }
}
