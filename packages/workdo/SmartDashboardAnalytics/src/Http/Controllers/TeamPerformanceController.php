<?php

namespace Workdo\SmartDashboardAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\SmartDashboardAnalytics\Services\TeamPerformanceService;

class TeamPerformanceController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::user()->can('manage-smart-team'))
        {
            $service = new TeamPerformanceService();
            $data = $service->getTeamData();

            return Inertia::render('SmartDashboardAnalytics/TeamPerformance/Index', $data);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}