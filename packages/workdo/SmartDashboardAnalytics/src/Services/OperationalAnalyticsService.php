<?php

namespace Workdo\SmartDashboardAnalytics\Services;

use Illuminate\Support\Facades\DB;

class OperationalAnalyticsService
{
    public function getOperationalData()
    {
        return [
            'inventory_management' => $this->getInventoryManagement(),
            'pos_analytics' => $this->getPosAnalytics(),
            'project_management' => $this->getProjectManagement(),
            'purchase_vendor_analytics' => $this->getPurchaseVendorAnalytics(),
        ];
    }

    private function getInventoryManagement()
    {
        $createdBy = creatorId();

        $totalProducts = DB::table('product_service_items')
            ->where('created_by', $createdBy)
            ->where('is_active', 1)
            ->count();

        $totalStockValue = DB::table('warehouse_stocks as ws')
            ->join('product_service_items as psi', 'ws.product_id', '=', 'psi.id')
            ->where('psi.created_by', $createdBy)
            ->where('psi.is_active', 1)
            ->sum(DB::raw('ws.quantity * psi.sale_price'));

        $outOfStock = DB::table('product_service_items as psi')
            ->leftJoin('warehouse_stocks as ws', 'ws.product_id', '=', 'psi.id')
            ->where('psi.created_by', $createdBy)
            ->where('psi.is_active', 1)
            ->selectRaw('psi.id, COALESCE(SUM(ws.quantity), 0) as total_qty')
            ->groupBy('psi.id')
            ->havingRaw('COALESCE(SUM(ws.quantity), 0) = 0')
            ->get()
            ->count();

        $activeWarehouses = DB::table('warehouses')
            ->where('created_by', $createdBy)
            ->where('is_active', 1)
            ->count();

        $stockByCategory = DB::table('product_service_categories as psc')
            ->leftJoin('product_service_items as psi', function ($join) use ($createdBy) {
                $join->on('psi.category_id', '=', 'psc.id')
                     ->where('psi.is_active', 1)
                     ->where('psi.created_by', $createdBy);
            })
            ->leftJoin('warehouse_stocks as ws', 'ws.product_id', '=', 'psi.id')
            ->selectRaw('psc.name as category_name, psc.color, COUNT(DISTINCT psi.id) as product_count, SUM(ws.quantity) as total_quantity, SUM(ws.quantity * psi.sale_price) as stock_value')
            ->groupBy('psc.id', 'psc.name', 'psc.color')
            ->where('psc.created_by', $createdBy)
            ->orderBy('stock_value', 'desc')
            ->get();

        $stockByWarehouse = DB::table('warehouses as w')
            ->leftJoin('warehouse_stocks as ws', 'ws.warehouse_id', '=', 'w.id')
            ->leftJoin('product_service_items as psi', 'psi.id', '=', 'ws.product_id')
            ->where('w.created_by', $createdBy)
            ->where('w.is_active', 1)
            ->selectRaw('w.name as warehouse_name, w.city, COUNT(DISTINCT ws.product_id) as product_count, SUM(ws.quantity) as total_units, SUM(ws.quantity * psi.sale_price) as stock_value')
            ->groupBy('w.id', 'w.name', 'w.city')
            ->orderBy('stock_value', 'desc')
            ->get();

        $productList = DB::table('product_service_items as psi')
            ->leftJoin('product_service_categories as psc', 'psi.category_id', '=', 'psc.id')
            ->leftJoin('warehouse_stocks as ws', 'ws.product_id', '=', 'psi.id')
            ->where('psi.created_by', $createdBy)
            ->where('psi.is_active', 1)
            ->select(
                'psi.id', 'psi.name as product_name', 'psi.sku',
                'psc.name as category_name', 'psc.color as category_color',
                'psi.type', 'psi.unit', 'psi.sale_price', 'psi.purchase_price',
                DB::raw('ROUND(psi.sale_price - psi.purchase_price, 2) as margin'),
                DB::raw('ROUND(((psi.sale_price - psi.purchase_price) / NULLIF(psi.sale_price, 0)) * 100, 2) as margin_percentage'),
                DB::raw('COALESCE(SUM(ws.quantity), 0) as total_stock'),
                DB::raw('COALESCE(SUM(ws.quantity * psi.sale_price), 0) as stock_value'),
                'psi.is_active',
                DB::raw("CASE WHEN COALESCE(SUM(ws.quantity), 0) = 0 THEN 'Out of Stock' WHEN COALESCE(SUM(ws.quantity), 0) < 10 THEN 'Low Stock' ELSE 'In Stock' END as stock_status")
            )
            ->groupBy('psi.id', 'psi.name', 'psi.sku', 'psc.name', 'psc.color', 'psi.type', 'psi.unit', 'psi.sale_price', 'psi.purchase_price', 'psi.is_active')
            ->orderBy('total_stock', 'asc')
            ->get();

        return [
            'kpi' => [
                'total_products' => $totalProducts,
                'total_stock_value' => $totalStockValue,
                'out_of_stock' => $outOfStock,
                'active_warehouses' => $activeWarehouses,
            ],
            'stock_by_category' => $stockByCategory,
            'stock_by_warehouse' => $stockByWarehouse,
            'product_list' => ['data' => $productList, 'total' => $productList->count()],
        ];
    }

    private function getPosAnalytics()
    {
        $createdBy = creatorId();

        $todayRevenue = DB::table('pos as p')
            ->join('pos_payments as pp', 'pp.pos_id', '=', 'p.id')
            ->where('p.created_by', $createdBy)
            ->whereDate('p.pos_date', now()->today())
            ->where('p.status', 'completed')
            ->sum('pp.amount');

        $todayTransactions = DB::table('pos')
            ->where('created_by', $createdBy)
            ->whereDate('pos_date', now()->today())
            ->where('status', 'completed')
            ->count();

        $avgTransactionValue = $todayTransactions > 0 ? round($todayRevenue / $todayTransactions, 2) : 0;

        $topProductToday = DB::table('pos_items as pi')
            ->join('pos as p', 'p.id', '=', 'pi.pos_id')
            ->join('product_service_items as psi', 'psi.id', '=', 'pi.product_id')
            ->where('p.created_by', $createdBy)
            ->whereDate('p.pos_date', now()->today())
            ->where('p.status', 'completed')
            ->selectRaw('psi.name as product_name, SUM(pi.quantity) as total_qty')
            ->groupBy('psi.id', 'psi.name')
            ->orderBy('total_qty', 'desc')
            ->first();

        // Generate all 24 hours first
        $allHours = collect(range(0, 23))->map(function($hour) {
            return [
                'sale_hour' => $hour,
                'transaction_count' => 0,
                'revenue' => 0,
                'total_discount' => 0
            ];
        })->keyBy('sale_hour');

        // Get actual data for hours with sales
        $actualHourlyData = DB::table('pos as p')
            ->join('pos_payments as pp', 'pp.pos_id', '=', 'p.id')
            ->where('p.created_by', $createdBy)
            ->whereDate('p.pos_date', now()->today())
            ->where('p.status', 'completed')
            ->selectRaw('HOUR(p.created_at) as sale_hour, COUNT(p.id) as transaction_count, SUM(pp.amount) as revenue, SUM(pp.discount_amount) as total_discount')
            ->groupBy(DB::raw('HOUR(p.created_at)'))
            ->get()
            ->keyBy('sale_hour');

        // Merge: actual data overrides default zeros
        $hourlyRevenue = $allHours->map(function($hourData, $hour) use ($actualHourlyData) {
            return $actualHourlyData->has($hour) ? $actualHourlyData->get($hour) : $hourData;
        })->values();

        $thisWeekRevenue = DB::table('pos as p')
            ->join('pos_payments as pp', 'pp.pos_id', '=', 'p.id')
            ->where('p.created_by', $createdBy)
            ->where('p.status', 'completed')
            ->whereDate('p.pos_date', '>=', now()->startOfWeek())
            ->whereDate('p.pos_date', '<=', now()->endOfWeek())
            ->sum('pp.amount');

        $lastWeekRevenue = DB::table('pos as p')
            ->join('pos_payments as pp', 'pp.pos_id', '=', 'p.id')
            ->where('p.created_by', $createdBy)
            ->where('p.status', 'completed')
            ->whereDate('p.pos_date', '>=', now()->subWeek()->startOfWeek())
            ->whereDate('p.pos_date', '<=', now()->subWeek()->endOfWeek())
            ->sum('pp.amount');

        $thisWeekReturns = DB::table('pos_returns')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['approved', 'completed'])
            ->whereDate('return_date', '>=', now()->startOfWeek())
            ->whereDate('return_date', '<=', now()->endOfWeek())
            ->sum('total_amount');

        $lastWeekReturns = DB::table('pos_returns')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['approved', 'completed'])
            ->whereDate('return_date', '>=', now()->subWeek()->startOfWeek())
            ->whereDate('return_date', '<=', now()->subWeek()->endOfWeek())
            ->sum('total_amount');

        $weekComparison = [
            [
                'metric' => 'Revenue',
                'this_week' => round($thisWeekRevenue, 2),
                'last_week' => round($lastWeekRevenue, 2)
            ],
            [
                'metric' => 'Returns',
                'this_week' => round($thisWeekReturns, 2),
                'last_week' => round($lastWeekReturns, 2)
            ]
        ];

        $topProducts = DB::table('pos_items as pi')
            ->join('pos as p', 'p.id', '=', 'pi.pos_id')
            ->join('product_service_items as psi', 'psi.id', '=', 'pi.product_id')
            ->leftJoin('product_service_categories as psc', 'psc.id', '=', 'psi.category_id')
            ->where('p.created_by', $createdBy)
            ->where('p.status', 'completed')
            ->whereDate('p.pos_date', '>=', now()->startOfWeek())
            ->selectRaw('psi.id, psi.name as product_name, psc.name as category_name, SUM(pi.quantity) as units_sold, SUM(pi.subtotal) as revenue, SUM(pi.tax_amount) as total_tax, SUM(pi.total_amount) as total_with_tax, AVG(pi.price) as avg_price')
            ->groupBy('psi.id', 'psi.name', 'psc.name')
            ->orderByRaw('SUM(pi.subtotal) DESC')
            ->limit(10)
            ->get();

        $transactions = DB::table('pos as p')
            ->leftJoin('users as u', 'u.id', '=', 'p.customer_id')
            ->leftJoin('customers as c', 'c.user_id', '=', 'p.customer_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'p.warehouse_id')
            ->where('p.created_by', $createdBy)
            ->select(
                'p.id', 'p.sale_number', 'p.pos_date', 'p.created_at as sale_time',
                DB::raw('COALESCE(c.company_name, u.name, "Walk-in") as customer_name'),
                DB::raw('COALESCE(c.customer_code, "-") as customer_code'),
                'w.name as warehouse_name', 'p.status'
            )
            ->orderBy('p.created_at', 'desc')
            ->get()
            ->map(function ($txn) {
                // Get aggregated data for each transaction
                $items = DB::table('pos_items as pi')
                    ->where('pi.pos_id', $txn->id)
                    ->selectRaw('COUNT(pi.id) as items_count, SUM(pi.quantity) as total_units')
                    ->first();
                
                $payment = DB::table('pos_payments as pp')
                    ->where('pp.pos_id', $txn->id)
                    ->select('discount', 'discount_amount', 'amount as total_paid')
                    ->first();
                
                $txn->items_count = $items->items_count ?? 0;
                $txn->total_units = $items->total_units ?? 0;
                $txn->discount_percentage = $payment->discount ?? 0;
                $txn->discount_amount = $payment->discount_amount ?? 0;
                $txn->total_paid = $payment->total_paid ?? 0;
                
                return $txn;
            });

        $topSellingProducts = DB::table('pos_items as pi')
            ->join('pos as p', 'p.id', '=', 'pi.pos_id')
            ->join('product_service_items as psi', 'psi.id', '=', 'pi.product_id')
            ->leftJoin('product_service_categories as psc', 'psc.id', '=', 'psi.category_id')
            ->where('p.created_by', $createdBy)
            ->where('p.status', 'completed')
            ->whereDate('p.pos_date', '>=', now()->startOfWeek())
            ->select(
                'psi.id', 'psi.name as product_name', 'psi.sku',
                'psc.name as category_name',
                'psi.sale_price as list_price', 'psi.purchase_price',
                DB::raw('SUM(pi.quantity) as units_sold'),
                DB::raw('SUM(pi.subtotal) as revenue'),
                DB::raw('SUM(pi.quantity * psi.purchase_price) as cost'),
                DB::raw('SUM(pi.subtotal) - SUM(pi.quantity * psi.purchase_price) as profit'),
                DB::raw('ROUND((SUM(pi.subtotal) - SUM(pi.quantity * psi.purchase_price)) / NULLIF(SUM(pi.subtotal), 0) * 100, 2) as margin_percentage')
            )
            ->groupBy('psi.id', 'psi.name', 'psi.sku', 'psc.name', 'psi.sale_price', 'psi.purchase_price')
            ->orderByRaw('SUM(pi.quantity) DESC')
            ->limit(10)
            ->get();

        return [
            'kpi' => [
                'today_revenue' => $todayRevenue,
                'today_transactions' => $todayTransactions,
                'avg_transaction_value' => $avgTransactionValue,
                'top_product_today' => $topProductToday->product_name ?? null,
            ],
            'hourly_revenue' => $hourlyRevenue,
            'week_comparison' => $weekComparison,
            'top_products' => $topProducts,
            'top_selling_products' => $topSellingProducts,
            'transactions' => ['data' => $transactions, 'total' => $transactions->count()],
        ];
    }

    private function getProjectManagement()
    {
        $createdBy = creatorId();

        $activeProjects = DB::table('projects')
            ->where('created_by', $createdBy)
            ->where('status', 'Ongoing')
            ->count();

        $completedThisMonth = DB::table('projects')
            ->where('created_by', $createdBy)
            ->where('status', 'Finished')
            ->whereMonth('updated_at', now()->month)
            ->count();

        $delayedProjects = DB::table('projects')
            ->where('created_by', $createdBy)
            ->where('end_date', '<', now())
            ->where('status', '!=', 'Finished')
            ->count();

        $budgetUtilPct = DB::table('projects as p')
            ->leftJoin('project_milestones as pm', 'pm.project_id', '=', 'p.id')
            ->where('p.created_by', $createdBy)
            ->selectRaw('COALESCE(SUM(p.budget), 0) as total_budget, COALESCE(SUM(pm.cost), 0) as total_milestone_cost')
            ->first();
        $budgetUtilization = $budgetUtilPct && $budgetUtilPct->total_budget > 0 ? round(($budgetUtilPct->total_milestone_cost / $budgetUtilPct->total_budget) * 100, 2) : 0;

        $statusDistribution = DB::table('projects')
            ->where('created_by', $createdBy)
            ->selectRaw('status, COUNT(*) as project_count, SUM(budget) as total_budget')
            ->groupBy('status')
            ->get();

        $budgetVsMilestone = DB::table('projects as p')
            ->leftJoin('project_milestones as pm', 'pm.project_id', '=', 'p.id')
            ->where('p.created_by', $createdBy)
            ->select(
                'p.id', 'p.name as project_name', 'p.budget', 'p.start_date', 'p.end_date', 'p.status',
                DB::raw('COALESCE(SUM(pm.cost), 0) as milestone_cost'),
                DB::raw('COALESCE(AVG(pm.progress), 0) as avg_progress'),
                DB::raw('COUNT(pm.id) as milestone_count')
            )
            ->groupBy('p.id', 'p.name', 'p.budget', 'p.start_date', 'p.end_date', 'p.status')
            ->orderBy('p.start_date', 'desc')
            ->get();

        $projectList = DB::table('projects as p')
            ->leftJoin('project_tasks as pt', 'pt.project_id', '=', 'p.id')
            ->leftJoin('task_stages as ts', 'ts.id', '=', 'pt.stage_id')
            ->leftJoin('project_milestones as pm', 'pm.project_id', '=', 'p.id')
            ->where('p.created_by', $createdBy)
            ->select(
                'p.id', 'p.name as project_name', 'p.budget', 'p.start_date', 'p.end_date', 'p.status',
                DB::raw('COUNT(DISTINCT pt.id) as total_tasks'),
                DB::raw('COUNT(DISTINCT CASE WHEN ts.complete = 1 THEN pt.id END) as completed_tasks'),
                DB::raw('COALESCE(SUM(DISTINCT pm.cost), 0) as milestone_cost'),
                DB::raw("CASE WHEN p.end_date < CURDATE() AND p.status != 'Finished' THEN 'Delayed' WHEN p.end_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND p.status != 'Finished' THEN 'At Risk' ELSE 'On Track' END as health_status")
            )
            ->groupBy('p.id', 'p.name', 'p.budget', 'p.start_date', 'p.end_date', 'p.status')
            ->orderBy('p.start_date', 'desc')
            ->get();

        return [
            'kpi' => [
                'active_projects' => $activeProjects,
                'completed_this_month' => $completedThisMonth,
                'delayed_projects' => $delayedProjects,
                'budget_utilization' => $budgetUtilization,
            ],
            'status_distribution' => $statusDistribution,
            'budget_vs_milestone' => $budgetVsMilestone,
            'project_list' => ['data' => $projectList, 'total' => $projectList->count()],
        ];
    }

    public function getPosDetail($posId)
    {
        $createdBy = creatorId();

        $pos = DB::table('pos as p')
            ->leftJoin('users as u', 'u.id', '=', 'p.customer_id')
            ->leftJoin('customers as c', 'c.user_id', '=', 'p.customer_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'p.warehouse_id')
            ->where('p.id', $posId)
            ->where('p.created_by', $createdBy)
            ->select(
                'p.id', 'p.sale_number', 'p.pos_date',
                DB::raw('COALESCE(c.company_name, u.name, "Walk-in") as customer_name'),
                'w.name as warehouse_name', 'p.status',
                'p.created_at as sale_time'
            )
            ->first();

        $items = DB::table('pos_items as pi')
            ->join('product_service_items as psi', 'psi.id', '=', 'pi.product_id')
            ->leftJoin('product_service_categories as psc', 'psc.id', '=', 'psi.category_id')
            ->where('pi.pos_id', $posId)
            ->select(
                'psi.id', 'psi.name as product_name', 'psi.sku',
                'psc.name as category_name',
                'pi.quantity', 'pi.price as unit_price', 'pi.subtotal',
                'pi.tax_amount', 'pi.total_amount'
            )
            ->get();

        return [
            'pos' => $pos,
            'items' => $items,
        ];
    }

    public function getProductDetail($productId)
    {
        $createdBy = creatorId();

        $warehouseBreakdown = DB::table('warehouse_stocks as ws')
            ->join('warehouses as w', 'w.id', '=', 'ws.warehouse_id')
            ->join('product_service_items as psi', 'psi.id', '=', 'ws.product_id')
            ->where('ws.product_id', $productId)
            ->select('w.name as warehouse_name', 'w.city', 'ws.quantity', DB::raw('ws.quantity * psi.sale_price as value'), 'ws.updated_at as last_updated')
            ->orderBy('ws.quantity', 'desc')
            ->get();

        $transfers = DB::table('transfers as t')
            ->join('warehouses as wf', 'wf.id', '=', 't.from_warehouse')
            ->join('warehouses as wt', 'wt.id', '=', 't.to_warehouse')
            ->where('t.product_id', $productId)
            ->select('t.date', 'wf.name as from_warehouse', 'wt.name as to_warehouse', 't.quantity', 't.created_at')
            ->orderBy('t.date', 'desc')
            ->limit(10)
            ->get();

        $posSales = DB::table('pos_items as pi')
            ->join('pos as p', 'p.id', '=', 'pi.pos_id')
            ->leftJoin('users as u', 'u.id', '=', 'p.customer_id')
            ->where('pi.product_id', $productId)
            ->where('p.status', 'completed')
            ->select('p.pos_date', 'p.sale_number', DB::raw('COALESCE(u.name, "Walk-in") as customer_name'), 'pi.quantity', 'pi.price', 'pi.total_amount')
            ->orderBy('p.pos_date', 'desc')
            ->limit(10)
            ->get();

        $purchases = DB::table('purchase_invoice_items as pii')
            ->join('purchase_invoices as piv', 'piv.id', '=', 'pii.invoice_id')
            ->join('users as u', 'u.id', '=', 'piv.vendor_id')
            ->leftJoin('vendors as v', 'v.user_id', '=', 'piv.vendor_id')
            ->where('pii.product_id', $productId)
            ->select('piv.invoice_date', 'piv.invoice_number', DB::raw('COALESCE(v.company_name, u.name) as vendor_name'), 'pii.quantity', 'pii.unit_price', 'pii.total_amount', 'piv.status')
            ->orderBy('piv.invoice_date', 'desc')
            ->limit(10)
            ->get();

        return [
            'warehouse_breakdown' => $warehouseBreakdown,
            'transfers' => $transfers,
            'pos_sales' => $posSales,
            'purchases' => $purchases,
        ];
    }

    public function getProjectDetail($projectId)
    {
        $createdBy = creatorId();

        $project = DB::table('projects')
            ->where('id', $projectId)
            ->where('created_by', $createdBy)
            ->select('id', 'name as project_name', 'description', 'budget', 'start_date', 'end_date', 'status')
            ->first();

        return ['project' => $project];
    }

    public function getProjectTasks($projectId)
    {
        $createdBy = creatorId();

        $tasks = DB::table('project_tasks as pt')
            ->leftJoin('task_stages as ts', 'ts.id', '=', 'pt.stage_id')
            ->where('pt.project_id', $projectId)
            ->where('pt.created_by', $createdBy)
            ->select(
                'pt.id', 'pt.title', 'pt.priority', 'pt.duration',
                DB::raw('COALESCE(ts.name, "Pending") as stage_name'),
                DB::raw('COALESCE(ts.color, "#9CA3AF") as stage_color'),
                DB::raw('CASE WHEN pt.status = "completed" OR ts.complete = 1 THEN 1 ELSE 0 END as is_completed'),
                'pt.created_at', 'pt.updated_at'
            )
            ->orderBy('pt.created_at')
            ->get();

        return ['tasks' => $tasks];
    }

    public function getProjectMilestones($projectId)
    {
        $createdBy = creatorId();

        $milestones = DB::table('project_milestones')
            ->where('project_id', $projectId)
            ->where('created_by', $createdBy)
            ->select('id', 'title', 'cost', 'start_date', 'end_date', 'status', 'progress', 'summary')
            ->orderBy('start_date')
            ->get();

        return ['milestones' => $milestones];
    }

    public function getInvoiceDetail($invoiceId)
    {
        $createdBy = creatorId();

        $lineItems = DB::table('purchase_invoice_items as pii')
            ->join('product_service_items as psi', 'psi.id', '=', 'pii.product_id')
            ->leftJoin('product_service_categories as psc', 'psc.id', '=', 'psi.category_id')
            ->where('pii.invoice_id', $invoiceId)
            ->select(
                'pii.id', 'psi.name as product_name', 'psi.sku',
                'psc.name as category_name',
                'pii.quantity', 'pii.unit_price', 'pii.discount_percentage', 'pii.discount_amount',
                'pii.tax_percentage', 'pii.tax_amount', 'pii.total_amount'
            )
            ->get();

        return ['line_items' => $lineItems];
    }

    public function getVendorInvoices($vendorId)
    {
        $createdBy = creatorId();

        $invoices = DB::table('purchase_invoices as pi')
            ->join('users as u', 'u.id', '=', 'pi.vendor_id')
            ->where('pi.vendor_id', $vendorId)
            ->where('pi.created_by', $createdBy)
            ->select(
                'pi.id', 'pi.invoice_number', 'pi.invoice_date', 'pi.due_date',
                'pi.subtotal', 'pi.tax_amount', 'pi.discount_amount',
                'pi.total_amount', 'pi.paid_amount', 'pi.balance_amount', 'pi.status'
            )
            ->orderBy('pi.invoice_date', 'desc')
            ->get();

        return ['invoices' => $invoices];
    }

    private function getPurchaseVendorAnalytics()
    {
        $createdBy = creatorId();

        $purchasesThisMonth = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->where('status', '!=', 'draft')
            ->whereMonth('invoice_date', now()->month)
            ->sum('total_amount');

        $pendingBills = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['posted', 'partial'])
            ->count();

        $pendingBillAmount = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['posted', 'partial'])
            ->sum('balance_amount');

        $overdueBills = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->where(function ($q) {
                $q->where('status', 'overdue')
                  ->orWhere(function ($q2) {
                      $q2->where('due_date', '<', now())
                         ->whereNotIn('status', ['paid', 'draft']);
                  });
            })
            ->count();

            $uniqueVendors = DB::table('users')->where('type', 'vendor')->where('created_by', $createdBy)->count();

        $monthlyPurchaseTrend = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->where('status', '!=', 'draft')
            ->where('invoice_date', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(invoice_date, "%Y-%m") as month, COUNT(*) as invoice_count, SUM(subtotal) as subtotal_amount, SUM(tax_amount) as total_tax, SUM(total_amount) as total_amount, SUM(paid_amount) as paid_amount, SUM(balance_amount) as outstanding_amount')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $purchaseStatusDist = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->selectRaw('status, COUNT(*) as invoice_count, SUM(total_amount) as total_amount, SUM(balance_amount) as outstanding')
            ->groupBy('status')
            ->get();

        $vendorSummary = DB::table('purchase_invoices as pi')
            ->join('users as u', 'u.id', '=', 'pi.vendor_id')
            ->leftJoin('vendors as v', 'v.user_id', '=', 'pi.vendor_id')
            ->where('pi.created_by', $createdBy)
            ->where('pi.status', '!=', 'draft')
            ->select(
                DB::raw('COALESCE(v.company_name, u.name) as vendor_name'),
                'v.vendor_code',
                DB::raw('COUNT(pi.id) as total_invoices'),
                DB::raw('SUM(pi.total_amount) as total_purchased'),
                DB::raw('SUM(pi.paid_amount) as total_paid'),
                DB::raw('SUM(pi.balance_amount) as total_outstanding'),
                DB::raw('COUNT(CASE WHEN pi.status = "overdue" THEN 1 END) as overdue_count'),
                DB::raw('COUNT(CASE WHEN pi.status IN ("posted","partial") THEN 1 END) as pending_count'),
                DB::raw('MAX(pi.invoice_date) as last_purchase_date'),
                DB::raw('AVG(pi.total_amount) as avg_invoice_value')
            )
            ->groupBy('u.id', 'v.company_name', 'u.name', 'v.vendor_code')
            ->orderBy('total_purchased', 'desc')
            ->get();

        $purchaseInvoices = DB::table('purchase_invoices as pi')
            ->join('users as u', 'u.id', '=', 'pi.vendor_id')
            ->leftJoin('vendors as v', 'v.user_id', '=', 'pi.vendor_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'pi.warehouse_id')
            ->leftJoin('purchase_invoice_items as pii', 'pii.invoice_id', '=', 'pi.id')
            ->where('pi.created_by', $createdBy)
            ->select(
                'pi.id', 'pi.invoice_number', 'pi.invoice_date', 'pi.due_date',
                DB::raw('COALESCE(v.company_name, u.name) as vendor_name'),
                'v.vendor_code', 'w.name as warehouse_name',
                'pi.subtotal', 'pi.tax_amount', 'pi.discount_amount', 'pi.total_amount',
                'pi.paid_amount', 'pi.balance_amount', 'pi.status',
                DB::raw('DATEDIFF(pi.due_date, CURDATE()) as days_until_due'),
                DB::raw('COUNT(pii.id) as line_items')
            )
            ->groupBy('pi.id', 'pi.invoice_number', 'pi.invoice_date', 'pi.due_date', 'v.company_name', 'u.name', 'v.vendor_code', 'w.name', 'pi.subtotal', 'pi.tax_amount', 'pi.discount_amount', 'pi.total_amount', 'pi.paid_amount', 'pi.balance_amount', 'pi.status')
            ->orderBy('pi.invoice_date', 'desc')
            ->get();

        $operationalExpenses = DB::table('expenses as e')
            ->join('expense_categories as ec', 'ec.id', '=', 'e.category_id')
            ->where('e.created_by', $createdBy)
            ->select(
                'e.id', 'e.expense_number',
                'e.expense_date',
                'e.description', 'e.amount', 'e.status',
                'ec.category_name as category_name'
            )
            ->orderBy('e.expense_date', 'desc')
            ->get();

        return [
            'kpi' => [
                'purchases_this_month' => $purchasesThisMonth,
                'pending_bills' => $pendingBills,
                'pending_bill_amount' => $pendingBillAmount,
                'overdue_bills' => $overdueBills,
                'unique_vendors' => $uniqueVendors,
            ],
            'monthly_trend' => $monthlyPurchaseTrend,
            'status_distribution' => $purchaseStatusDist,
            'vendor_summary' => $vendorSummary,
            'purchase_invoices' => ['data' => $purchaseInvoices, 'total' => $purchaseInvoices->count()],
            'operational_expenses' => ['data' => $operationalExpenses, 'total' => $operationalExpenses->count()],
        ];
    }
}