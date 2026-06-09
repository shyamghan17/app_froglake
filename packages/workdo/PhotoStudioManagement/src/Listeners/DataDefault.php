<?php

namespace Workdo\PhotoStudioManagement\Listeners;

use App\Events\DefaultData;
use Workdo\PhotoStudioManagement\Models\PhotoStudioCustomPage;

class DataDefault
{
    public function __construct() {}

    public function handle(DefaultData $event)
    {
        $company_id  = $event->company_id;
        $user_module = $event->user_module ? explode(',', $event->user_module) : [];

        if (!empty($user_module) && in_array('PhotoStudioManagement', $user_module)) {
            PhotoStudioCustomPage::defaultdata($company_id);
        }
    }
}
