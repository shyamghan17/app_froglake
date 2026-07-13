<?php

namespace Workdo\SmartDashboardAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\SmartDashboardAnalytics\Services\OperationalAnalyticsService;

class OperationalAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->can('manage-smart-operations')) {
            $service = new OperationalAnalyticsService();
            $data = $service->getOperationalData();

            return Inertia::render('SmartDashboardAnalytics/OperationalAnalytics/Index', $data);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function posDetail($posId)
    {
        if (Auth::user()->can('manage-smart-operations')) {
            $service = new OperationalAnalyticsService();
            return response()->json($service->getPosDetail($posId));
        } else {
            return response()->json(['error' => __('Permission denied')], 403);
        }
    }
}
