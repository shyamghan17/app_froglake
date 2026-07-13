<?php

namespace Workdo\SmartDashboardAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\SmartDashboardAnalytics\Services\ExecutiveOverviewService;

class ExecutiveDashboardController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-smart-dashboard'))
        {
            $service = new ExecutiveOverviewService();
            $data = $service->getOverviewData();

            return Inertia::render('SmartDashboardAnalytics/ExecutiveDashboard/Index', $data);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}