<?php

namespace Workdo\SmartDashboardAnalytics\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExecutiveOverviewService
{
    /**
     * Get all aggregated data for Executive Overview Dashboard.
     */
    public function getOverviewData()
    {
        return [
            'kpi_cards' => $this->getKpiCards(),
            'quick_insights' => $this->getQuickInsights(),
            'module_summaries' => $this->getModuleSummaries(),
            'top_employees' => $this->getTopEmployees(),
            'top_customers' => $this->getTopCustomers(),
            'recent_transactions' => $this->getRecentTransactions(),
        ];
    }

    private function formatCompanyCurrency($amount)
    {
        $companySetting = getCompanyAllSetting();

        $symbol = $companySetting['currencySymbol'] ?? '$';
        $decimalFormat = (int) ($companySetting['decimalFormat'] ?? 2);
        $decimalSeparator = $companySetting['decimalSeparator'] ?? '.';
        $thousandsSeparator = $companySetting['thousandsSeparator'] ?? ',';
        $symbolPosition = $companySetting['currencySymbolPosition'] ?? __('before');
        $symbolSpace = ($companySetting['currencySymbolSpace'] ?? '0') === '1' ? ' ' : '';

        $formatted = number_format((float) $amount, $decimalFormat, $decimalSeparator, $thousandsSeparator);

        if ($symbolPosition === 'after') {
            return $formatted . $symbolSpace . $symbol;
        }

        return $symbol . $symbolSpace . $formatted;
    }


    private function getKpiCards()
    {
        $createdBy = creatorId();
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        $revenueCurrent = DB::table('journal_entries')
            ->where('created_by', $createdBy)
            ->where('status', 'posted')
            ->whereIn('reference_type', ['sales_invoice', 'pos_sale', 'revenue'])
            ->whereMonth('journal_date', $currentMonth)
            ->sum('total_credit');

        $revenueLast = DB::table('journal_entries')
            ->where('created_by', $createdBy)
            ->where('status', 'posted')
            ->whereIn('reference_type', ['sales_invoice', 'pos_sale', 'revenue'])
            ->whereMonth('journal_date', $lastMonth)
            ->sum('total_credit');

        $revenueTrend = DB::table('revenues')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->where('created_by', $createdBy)
            ->where('status', 'posted')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $expensesCurrent = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->whereMonth('je.journal_date', $currentMonth)
            ->sum('jei.debit_amount');

        $activeEmployees = DB::table('employees')
            ->where('created_by', $createdBy)
            ->where('user_id', '!=', null)
            ->count();

        $newHires = DB::table('employees')
            ->where('created_by', $createdBy)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        $terminationsThisMonth = DB::table('terminations')
            ->where('created_by', $createdBy)
            ->whereMonth('termination_date', $currentMonth)
            ->count();

        $activeProjects = DB::table('projects')
            ->where('created_by', $createdBy)
            ->where('status', 'Ongoing')
            ->count();

        $completedProjects = DB::table('projects')
            ->where('created_by', $createdBy)
            ->where('status', 'Finished')
            ->count();

        $onHoldProjects = DB::table('projects')
            ->where('created_by', $createdBy)
            ->where('status', 'Onhold')
            ->count();

        $todayAttendance = DB::table('attendances')
            ->where('created_by', $createdBy)
            ->whereDate('date', now()->today())
            ->selectRaw('
                ROUND((COUNT(CASE WHEN status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) as percentage
            ')
            ->first()->percentage ?? 0;

        $monthlyAverageAttendance = DB::table('attendances')
            ->where('created_by', $createdBy)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->selectRaw('
                ROUND((COUNT(CASE WHEN status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) as avg_pct
            ')
            ->first()->avg_pct ?? 0;

        $activeLeads = DB::table('leads')
            ->where('created_by', $createdBy)
            ->where('is_active', 1)
            ->count();

        $totalLeads = DB::table('leads')
            ->where('created_by', $createdBy)
            ->count();

        $dealsWon = DB::table('leads')
            ->where('created_by', $createdBy)
            ->where('is_converted', '!=', 0)
            ->count();

        $pipelineValue = DB::table('deals')
            ->where('created_by', $createdBy)
            ->where('is_active', 0)
            ->sum('price');

        return [
            'revenue' => [
                'current' => $revenueCurrent,
                'previous' => $revenueLast,
                'growth' => $revenueLast > 0 ? round(($revenueCurrent - $revenueLast) / $revenueLast * 100, 2) : 0,
                'trend' => $revenueTrend,
            ],
            'profit' => [
                'revenue' => $revenueCurrent,
                'expenses' => $expensesCurrent,
                'net' => $revenueCurrent - $expensesCurrent,
                'margin' => $revenueCurrent > 0 ? round(($revenueCurrent - $expensesCurrent) / $revenueCurrent * 100, 2) : 0,
            ],
            'employees' => [
                'active' => $activeEmployees,
                'new_hires' => $newHires,
                'attrition_rate' => $activeEmployees > 0 ? round(($terminationsThisMonth / $activeEmployees) * 100, 2) : 0,
            ],
            'projects' => [
                'active' => $activeProjects,
                'completed' => $completedProjects,
                'on_hold' => $onHoldProjects,
            ],
            'attendance' => [
                'today' => $todayAttendance,
                'monthly_average' => $monthlyAverageAttendance,
            ],
            'sales_pipeline' => [
                'active_leads' => $activeLeads,
                'pipeline_value' => $pipelineValue,
                'conversion_rate' => $totalLeads > 0 ? round(($dealsWon / $totalLeads) * 100, 2) : 0,
            ],
        ];
    }

    private function getQuickInsights()
    {
        $createdBy = creatorId();
        $insights = [];

        // 1. Positive revenue growth
        $revenueCurrent = DB::table('revenues')
            ->where('created_by', $createdBy)
            ->where('status', 'posted')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $revenueLast = DB::table('revenues')
            ->where('created_by', $createdBy)
            ->where('status', 'posted')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('amount');

        if ($revenueLast > 0 && $revenueCurrent > $revenueLast) {
            $growth = round(($revenueCurrent - $revenueLast) / $revenueLast * 100, 1);
            $insights[] = [
                'type' => 'positive',
                'title' => __('Revenue Growth'),
                'message' => $growth . __("% growth achieved over last month. Keep it up!")
            ];
        } elseif ($revenueLast > 0 && $revenueCurrent < $revenueLast) {
            $decline = round(($revenueLast - $revenueCurrent) / $revenueLast * 100, 1);
            $insights[] = [
                'type' => 'warning',
                'title' => __('Revenue Decline'),
                'message' => $decline . __("% revenue decline compared to last month. Review the sales pipeline.")
            ];
        }

        // 2. High conversion rate
        $totalLeads = DB::table('leads')->where('created_by', $createdBy)->count();
        $convertedLeads = DB::table('leads')->where('created_by', $createdBy)->where('is_converted', '!=', 0)->count();
        if ($totalLeads > 10) {
            $conversionRate = round(($convertedLeads / $totalLeads) * 100, 1);
            if ($conversionRate >= 30) {
                $insights[] = [
                    'type' => 'positive',
                    'title' => __('Strong Conversion Rate'),
                    'message' => $conversionRate . __("% lead conversion rate achieved. Your sales team is performing well.")
                ];
            }
        }

        // 3. Pending leave requests
        $pendingLeaves = DB::table('leave_applications')
            ->where('created_by', $createdBy)
            ->where('status', 'pending')
            ->count();

        if ($pendingLeaves > 5) {
            $insights[] = [
                'type' => 'info',
                'title' => __('Pending Leave Requests'),
                'message' => $pendingLeaves . __(" leave request(s) awaiting approval. Take action promptly."),
            ];
        }

        // 4. Low attendance today
        $todayRate = DB::table('attendances')
            ->where('created_by', $createdBy)
            ->whereDate('date', now()->today())
            ->selectRaw('ROUND((COUNT(CASE WHEN status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) as pct')
            ->first()->pct ?? 100;

        if ($todayRate > 0 && $todayRate < 50) {
            $insights[] = [
                'type' => 'warning',
                'title' => __('Low Attendance Today'),
                'message' => $todayRate . __("% attendance recorded today, which is below the 50% threshold.")
            ];
        }

        // 5. Delayed projects
        $delayedProjects = DB::table('projects')
            ->where('created_by', $createdBy)
            ->where('end_date', '<', now())
            ->where('status', '!=', 'Finished')
            ->count();

        if ($delayedProjects > 0) {
            $insights[] = [
                'type' => 'warning',
                'title' => __('Delayed Projects'),
                'message' => $delayedProjects .  __(" project(s) have passed their end date without completion."),
            ];
        }

        // 6. Low stock alert
        $lowStockCount = DB::table('warehouse_stocks')
            ->join('product_service_items', 'warehouse_stocks.product_id', '=', 'product_service_items.id')
            ->where('product_service_items.created_by', $createdBy)
            ->where('warehouse_stocks.quantity', '>', 0)
            ->where('warehouse_stocks.quantity', '<', 10)
            ->count(DB::raw('DISTINCT product_service_items.id'));

        if ($lowStockCount > 0) {
            $insights[] = [
                'type' => 'critical',
                'title' => __('Low Stock Alert'),
                'message' => $lowStockCount . __(" product(s) have less than 10 units in stock."),
            ];
        }

        // 7. Overdue invoices
        $overdueInvoices = DB::table('sales_invoices')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['posted', 'partial'])
            ->where('due_date', '<=', now())
            ->count();

        if ($overdueInvoices > 0) {
            $overdueAmount = DB::table('sales_invoices')
                ->where('created_by', $createdBy)
                ->whereIn('status', ['posted', 'partial'])
                ->where('due_date', '<=', now())
                ->sum('balance_amount');

            $formattedOverdueAmount = $this->formatCompanyCurrency($overdueAmount);

            $insights[] = [
                'type' => 'critical',
                'title' => __('Overdue Invoices'),
                'message' => $overdueInvoices .  __(" invoice(s) overdue totaling ") . $formattedOverdueAmount,
            ];
        }

        // 8. Profit margin check
        $expensesCurrent = DB::table('expenses')
            ->where('created_by', $createdBy)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        if ($revenueCurrent > 0) {
            $profitMargin = round(($revenueCurrent - $expensesCurrent) / $revenueCurrent * 100, 1);
            if ($profitMargin < 10) {
                $insights[] = [
                    'type' => 'critical',
                    'title' => __('Low Profit Margin'),
                    'message' => $profitMargin . __("% profit margin recorded. This is below the healthy threshold.")
                ];
            }
        }

        // Return max 8 insights
        return array_slice($insights, 0, 8);
    }

    private function getModuleSummaries()
    {
        $createdBy = creatorId();

        $totalEmployee = DB::table('employees')
            ->where('created_by', $createdBy)
            ->where('user_id', '!=', null)
            ->count();

        $todayAttendance = DB::table('attendances')
            ->where('created_by', $createdBy)
            ->whereDate('date', now()->today())
            ->where('status', 'present')
            ->count();

        $pendingLeaves = DB::table('leave_applications')
            ->where('created_by', $createdBy)
            ->where('status', 'pending')
            ->count();

        $activeLeads = DB::table('leads')
            ->where('created_by', $createdBy)
            ->where('is_active', 1)
            ->count();

        $conversionRate = DB::table('leads')
            ->where('created_by', $createdBy)
            ->selectRaw('ROUND((COUNT(CASE WHEN is_converted != 0 THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) as rate')
            ->first()
            ->rate ?? 0;

        $pipelineValue = DB::table('deals')
            ->where('created_by', $createdBy)
            ->where('is_active', 0)
            ->sum('price');

        $cashBalance = DB::table('chart_of_accounts as coa')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('coa.created_by', $createdBy)
            ->where('ac.type', 'assets')
            ->where('coa.account_code', 'LIKE', '10%')
            ->sum('coa.current_balance') ?? 0;

        $pendingInvoices = DB::table('sales_invoices')
            ->where('created_by', $createdBy)
            ->where('status', 'draft')
            ->count();

        $pendingBills = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->where('status', 'draft')
            ->count();

        $todaySales = DB::table('pos')
            ->where('created_by', $createdBy)
            ->whereDate('pos_date', now()->today())
            ->where('status', 'completed')
            ->sum(DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM pos_payments WHERE pos_id = pos.id)'));

        $todayTransactions = DB::table('pos')
            ->where('created_by', $createdBy)
            ->whereDate('pos_date', now()->today())
            ->where('status', 'completed')
            ->count();

        // POS: Top products (top 3 by units sold today)
        $posTopProducts = DB::table('pos_items as pi')
            ->join('pos as p', 'p.id', '=', 'pi.pos_id')
            ->join('product_service_items as psi', 'psi.id', '=', 'pi.product_id')
            ->where('p.created_by', $createdBy)
            ->where('p.status', 'completed')
            ->whereDate('p.pos_date', now()->today())
            ->selectRaw('psi.name as product_name, SUM(pi.quantity) as units_sold, SUM(pi.subtotal) as revenue')
            ->groupBy('psi.id', 'psi.name')
            ->orderBy('units_sold', 'desc')
            ->limit(3)
            ->get();

        // Projects: Active projects, completion rate, budget status
        $activeProjects = DB::table('projects')
            ->where('created_by', $createdBy)
            ->where('status', 'Ongoing')
            ->count();

        $totalProjects = DB::table('projects')
            ->where('created_by', $createdBy)
            ->count();

        $completedProjects = DB::table('projects')
            ->where('created_by', $createdBy)
            ->where('status', 'Finished')
            ->count();

        $completionRate = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 2) : 0;

        // Projects: Total budget and total milestone cost
        $totalBudget = DB::table('projects')
            ->where('created_by', $createdBy)
            ->sum('budget');

        $totalMilestoneCost = DB::table('project_milestones as pm')
            ->join('projects as p', 'p.id', '=', 'pm.project_id')
            ->where('p.created_by', $createdBy)
            ->sum('pm.cost');

        $budgetStatus = $totalBudget > 0 ? round(($totalMilestoneCost / $totalBudget) * 100, 2) : 0;

        // Sales: Monthly revenue, quotations count, orders count
        $monthlyRevenue = DB::table('sales_invoices')
            ->where('created_by', $createdBy)
            ->whereMonth('invoice_date', now()->month)
            ->where('status', 'paid')
            ->sum('total_amount');

        $quotationsCount = DB::table('sales_proposals')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['draft', 'sent'])
            ->count();

        $ordersCount = DB::table('sales_proposals')
            ->where('created_by', $createdBy)
            ->where('status', 'accepted')
            ->count();

        $quotationsValue = DB::table('sales_proposals')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['draft', 'sent'])
            ->sum('total_amount');

        $ordersValue = DB::table('sales_proposals')
            ->where('created_by', $createdBy)
            ->where('status', 'accepted')
            ->sum('total_amount');

        // Inventory: Total stock value + low stock + out of stock
        $totalStockValue = DB::table('warehouse_stocks as ws')
            ->join('product_service_items as psi', 'psi.id', '=', 'ws.product_id')
            ->where('psi.created_by', $createdBy)
            // ->where('psi.is_active', 1)
            ->sum(DB::raw('ws.quantity * psi.sale_price'));

        $lowStockItems = DB::table('warehouse_stocks as ws')
            ->join('product_service_items as psi', 'psi.id', '=', 'ws.product_id')
            ->where('psi.created_by', $createdBy)
            ->where('psi.is_active', 1)
            ->where('ws.quantity', '>', 0)
            ->where('ws.quantity', '<', 10)
            ->count(DB::raw('DISTINCT psi.id'));

        $outOfStock = DB::table('warehouse_stocks as ws')
            ->join('product_service_items as psi', 'psi.id', '=', 'ws.product_id')
            ->where('psi.created_by', $createdBy)
            ->where('psi.is_active', 1)
            ->where('ws.quantity', 0)
            ->count(DB::raw('DISTINCT psi.id'));

        return [
            'hrm' => [
                'total_employees' => $totalEmployee,
                'today_attendance' => $todayAttendance,
                'pending_leaves' => $pendingLeaves,
            ],
            'crm' => [
                'active_leads' => $activeLeads,
                'conversion_rate' => $conversionRate,
                'pipeline_value' => $pipelineValue,
            ],
            'account' => [
                'cash_balance' => $cashBalance,
                'pending_invoices' => $pendingInvoices,
                'pending_bills' => $pendingBills,
            ],
            'pos' => [
                'today_sales' => $todaySales,
                'today_transactions' => $todayTransactions,
                'top_products' => $posTopProducts,
            ],
            'projects' => [
                'active_projects' => $activeProjects,
                'completion_rate' => $completionRate,
                'total_budget' => $totalBudget,
                'total_milestone_cost' => $totalMilestoneCost,
                'budget_status_pct' => $budgetStatus,
            ],
            'sales' => [
                'monthly_revenue' => $monthlyRevenue,
                'quotations_count' => $quotationsCount,
                'quotations_value' => $quotationsValue,
                'orders_count' => $ordersCount,
                'orders_value' => $ordersValue,
            ],
            'inventory' => [
                'total_stock_value' => $totalStockValue,
                'low_stock_items' => $lowStockItems,
                'out_of_stock' => $outOfStock,
            ],
        ];
    }

    private function getTopEmployees()
    {
        $createdBy = (int) creatorId();
        $currentYearMonth = now()->format('Y-m');
        $taskAssignmentMatchSql = static function (string $column): string {
            return sprintf(
                '(JSON_CONTAINS(%1$s, CAST(employees.user_id AS CHAR), "$") OR JSON_CONTAINS(%1$s, JSON_QUOTE(CAST(employees.user_id AS CHAR)), "$"))',
                $column
            );
        };

        $totalTasksSql = sprintf(
            '(SELECT COUNT(*) FROM project_tasks WHERE project_tasks.created_by = %d AND %s) as total_tasks',
            $createdBy,
            $taskAssignmentMatchSql('project_tasks.assigned_to')
        );

        $tasksCompletedSql = sprintf(
            '(SELECT COUNT(*) FROM project_tasks pt JOIN task_stages ts ON pt.stage_id = ts.id AND ts.complete = 1 WHERE pt.created_by = %d AND %s) as tasks_completed',
            $createdBy,
            $taskAssignmentMatchSql('pt.assigned_to')
        );

        return DB::table('employees')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.created_by', $createdBy)
            ->where('users.type', 'staff')
            ->whereNotNull('employees.user_id')
            ->select(
                'employees.id',
                'employees.employee_id',
                'users.name as employee_name',
                'departments.department_name',
                'designations.designation_name',
                DB::raw($totalTasksSql),
                DB::raw($tasksCompletedSql),
                DB::raw('(SELECT ROUND((COUNT(CASE WHEN status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) FROM attendances WHERE created_by = ' . $createdBy . ' AND employee_id = employees.user_id AND DATE_FORMAT(date, "%Y-%m") = "' . $currentYearMonth . '") as attendance_percentage'),
                DB::raw('(SELECT COALESCE(SUM(total_hour), 0) FROM attendances WHERE created_by = ' . $createdBy . ' AND employee_id = employees.user_id AND DATE_FORMAT(date, "%Y-%m") = "' . $currentYearMonth . '") as hours_worked'),
                DB::raw('ROUND(
                    COALESCE((SELECT (COUNT(CASE WHEN status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) FROM attendances WHERE created_by = ' . $createdBy . ' AND employee_id = employees.user_id AND DATE_FORMAT(date, "%Y-%m") = "' . $currentYearMonth . '"), 0) * 40
                    + COALESCE((SELECT COUNT(*) FROM project_tasks pt JOIN task_stages ts ON pt.stage_id = ts.id AND ts.complete = 1 WHERE pt.created_by = ' . $createdBy . ' AND ' . $taskAssignmentMatchSql('pt.assigned_to') . '), 0) * 5
                , 2) as productivity_score')
            )
            ->orderBy('productivity_score', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($emp) {
                $emp->employee_id = $emp->employee_id ?? '';
                $emp->designation_name = $emp->designation_name ?? '';
                return $emp;
            });
    }

    private function getTopCustomers()
    {
        $createdBy = creatorId();

        $customers = DB::table('users as u')
            ->leftJoin('sales_invoices as si', function ($join) use ($createdBy) {
                $join->on('si.customer_id', '=', 'u.id')
                    ->where('si.created_by', '=', $createdBy)
                    ->whereIn('si.status', ['posted', 'partial', 'paid']);
            })
            ->where('u.created_by', $createdBy)
            ->where('u.type', 'client')
            ->select(
                'u.id as customer_id',
                'u.name as customer_name',
                DB::raw('COUNT(si.id) as total_orders'),
                DB::raw('COALESCE(SUM(si.total_amount), 0) as gross_revenue'),
                DB::raw('COALESCE(SUM(si.paid_amount), 0) as invoice_paid'),
                DB::raw('COALESCE(SUM(si.balance_amount), 0) as outstanding'),
                DB::raw('MAX(si.invoice_date) as last_purchase_date')
            )
            ->groupBy('u.id', 'u.name')
            ->orderByDesc('gross_revenue')
            ->limit(10)
            ->get();

        // If no customers at all, return empty array
        if ($customers->isEmpty()) {
            return [];
        }

        // Enrich with returns
        $customerIds = $customers->pluck('customer_id')->toArray();

        $returnsByCustomer = collect();
        if (!empty($customerIds)) {
            $returnsByCustomer = DB::table('sales_invoice_returns as sir')
                ->join('sales_invoices as si', 'sir.original_invoice_id', '=', 'si.id')
                ->whereIn('si.customer_id', $customerIds)
                ->whereIn('sir.status', ['approved', 'completed'])
                ->select('si.customer_id', DB::raw('COALESCE(SUM(sir.total_amount), 0) as returns_total'))
                ->groupBy('si.customer_id')
                ->get()
                ->keyBy('customer_id');
        }

        // Enrich with credit notes
        $creditsByCustomer = collect();
        if (!empty($customerIds)) {
            $creditsByCustomer = DB::table('credit_note_applications as cna')
                ->join('credit_notes as cn', 'cna.credit_note_id', '=', 'cn.id')
                ->whereIn('cn.customer_id', $customerIds)
                ->select('cn.customer_id', DB::raw('COALESCE(SUM(cna.applied_amount), 0) as credits_applied'))
                ->groupBy('cn.customer_id')
                ->get()
                ->keyBy('customer_id');
        }

        // Enrich with payments
        $paymentsByCustomer = collect();
        if (!empty($customerIds)) {
            $paymentsByCustomer = DB::table('customer_payments as cp')
                ->whereIn('cp.customer_id', $customerIds)
                ->where('cp.status', 'cleared')
                ->select('cp.customer_id', DB::raw('COALESCE(SUM(cp.payment_amount), 0) as total_payments'))
                ->groupBy('cp.customer_id')
                ->get()
                ->keyBy('customer_id');
        }

        return $customers->map(function ($c) use ($returnsByCustomer, $creditsByCustomer, $paymentsByCustomer) {
            $returns = (float) ($returnsByCustomer[$c->customer_id]->returns_total ?? 0);
            $credits = (float) ($creditsByCustomer[$c->customer_id]->credits_applied ?? 0);
            $payments = (float) ($paymentsByCustomer[$c->customer_id]->total_payments ?? 0);

            $c->returns_total = $returns;
            $c->credits_applied = $credits;
            $c->net_revenue = (float) (($c->gross_revenue ?? 0) - $returns - $credits);
            $c->total_paid = (float) (($c->invoice_paid ?? 0) + $payments);
            $c->total_payments = $payments;
            $c->invoice_paid_amount = (float) ($c->invoice_paid ?? 0);
            $c->average_order_value = ($c->total_orders > 0) ? round($c->net_revenue / $c->total_orders, 2) : 0;
            $c->status = ($c->last_purchase_date && strtotime($c->last_purchase_date) >= strtotime('-90 days')) ? 'Active' : 'Inactive';
            return $c;
        })->sortByDesc('net_revenue')->values();
    }

    private function getRecentTransactions()
    {
        $createdBy = creatorId();

        $revenues = DB::table('revenues')
            ->where('created_by', $createdBy)
            ->selectRaw('revenue_date as date, "Revenue" as type, "Account" as module, amount, status');

        $expenses = DB::table('expenses')
            ->where('created_by', $createdBy)
            ->selectRaw('expense_date as date, "Expense" as type, "Account" as module, amount, "posted" as status');

        // POS transactions (join with pos_payments to get the amount)
        $posPayments = DB::table('pos')
            ->join('pos_payments', 'pos_payments.pos_id', '=', 'pos.id')
            ->where('pos.created_by', $createdBy)
            ->selectRaw("DATE_FORMAT(pos.pos_date, '%Y-%m-%d') as date, 'POS Sale' as type, 'POS' as module, pos_payments.amount, pos.status");

        return DB::table(DB::raw("({$revenues->toSql()} UNION ALL {$expenses->toSql()} UNION ALL {$posPayments->toSql()}) as transactions"))
            ->mergeBindings($revenues)
            ->mergeBindings($expenses)
            ->mergeBindings($posPayments)
            ->orderBy('date', 'desc')
            ->get();
    }
}
