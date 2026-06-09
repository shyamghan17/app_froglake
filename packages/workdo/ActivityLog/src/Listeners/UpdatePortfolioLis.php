<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Portfolio\Events\UpdatePortfolio;

class UpdatePortfolioLis
{
    public function handle(UpdatePortfolio $event)
    {
        if (Module_is_active('ActivityLog')) {
            $portfolio = $event->portfolio;

            $activity = new AllActivityLog();
            $activity['module'] = 'Portfolio';
            $activity['sub_module'] = 'Portfolio';
            $activity['description'] = __('Portfolio updated by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $portfolio->created_by;
            $activity->save();
        }
    }
}
