<?php

namespace Workdo\SmartDashboardAnalytics\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\SmartDashboardAnalytics\Services\FinancialAnalyticsService;

class FinancialAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::user()->can('manage-smart-financial'))
        {
            $service = new FinancialAnalyticsService();
            $data = $service->getFinancialData($request);

            return Inertia::render('SmartDashboardAnalytics/FinancialAnalytics/Index', $data);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function revenueTransactions(Request $request)
    {
        if(Auth::user()->can('manage-smart-financial'))
        {
            $service = new FinancialAnalyticsService();
            $data = $service->getRevenueTransactions($request);

            return response()->json($data);
        } else {
            return response()->json(['error' => __('Permission denied')], 403);
        }
    }

    public function expenseTransactions(Request $request)
    {
        if(Auth::user()->can('manage-smart-financial'))
        {
            $service = new FinancialAnalyticsService();
            $data = $service->getExpenseTransactions($request);

            return response()->json($data);
        } else {
            return response()->json(['error' => __('Permission denied')], 403);
        }
    }

    public function journalEntries(Request $request)
    {
        if(Auth::user()->can('manage-smart-financial'))
        {
            $service = new FinancialAnalyticsService();
            $data = $service->getJournalEntriesFiltered($request);

            return response()->json($data);
        } else {
            return response()->json(['error' => __('Permission denied')], 403);
        }
    }
}