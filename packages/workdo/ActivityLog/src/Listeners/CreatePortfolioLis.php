<?php

namespace Workdo\ActivityLog\Listeners;

use Workdo\ActivityLog\Models\AllActivityLog;
use Illuminate\Support\Facades\Auth;
use Workdo\Portfolio\Events\CreatePortfolio;

class CreatePortfolioLis
{
    public function handle(CreatePortfolio $event)
    {
        if (Module_is_active('ActivityLog')) {
            $portfolio = $event->portfolio;

            $activity = new AllActivityLog();
            $activity['module'] = 'Portfolio';
            $activity['sub_module'] = 'Portfolio';
            $activity['description'] = __('New Portfolio created by the ');
            $activity['creator_id'] = Auth::user()->id;
            $activity['created_by'] = $portfolio->created_by;
            $activity->save();
        }
    }
}
