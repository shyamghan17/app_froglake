<?php

namespace Workdo\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\EBilling\Models\EBillingItem;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::user()->can('manage-ebilling')){
            $totalItems = EBillingItem::where('created_by', creatorId())->count();
            $activeItems = EBillingItem::where('created_by', creatorId())->where('is_active', true)->count();
            $recentItems = EBillingItem::where('created_by', creatorId())->latest()->take(5)->get();

            return Inertia::render('EBilling/Index', [
                'stats' => [
                    'total_items' => $totalItems,
                    'active_items' => $activeItems,
                    'inactive_items' => $totalItems - $activeItems,
                ],
                'recent_items' => $recentItems,
                'message' => __('EBilling Dashboard - Manage your items efficiently.')
            ]);
        }
        return back()->with('error', __('Permission denied'));
    }
}