<?php

namespace Workdo\SmartDashboardAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\SmartDashboardAnalytics\Services\SalesAnalyticsService;

class SalesAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::user()->can('manage-smart-sales'))
        {
            $service = new SalesAnalyticsService();
            $data = $service->getSalesData();

            return Inertia::render('SmartDashboardAnalytics/SalesAnalytics/Index', $data);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}