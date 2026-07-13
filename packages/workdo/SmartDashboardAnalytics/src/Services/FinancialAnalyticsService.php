<?php

namespace Workdo\SmartDashboardAnalytics\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialAnalyticsService
{
    public function getFinancialData(Request $request = null)
    {
        return [
            'revenue_analysis' => $this->getRevenueAnalysis($request),
            'expense_analysis' => $this->getExpenseAnalysis($request),
            'profitability' => $this->getProfitability(),
            'cash_flow' => $this->getCashFlow(),
            'journal_entries' => $this->getJournalEntries(),
        ];
    }

    private function getRevenueAnalysis(Request $request = null)
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

        $revenueBySource = DB::table('journal_entries')
            ->selectRaw('reference_type, CAST(SUM(total_credit) AS DECIMAL(20,2)) as revenue_amount')
            ->where('created_by', $createdBy)
            ->where('status', 'posted')
            ->where('entry_type', 'automatic')
            ->whereIn('reference_type', ['sales_invoice', 'pos_sale', 'revenue', 'service_invoice'])
            ->whereMonth('journal_date', $currentMonth)
            ->groupBy('reference_type')
            ->get()
            ->map(function ($item) {
                $item->revenue_amount = (float) $item->revenue_amount;
                return $item;
            });

        $revenueTrend = DB::table('journal_entries')
            ->selectRaw('DATE_FORMAT(journal_date, "%Y-%m") as month, SUM(total_credit) as total')
            ->where('created_by', $createdBy)
            ->where('status', 'posted')
            ->whereIn('reference_type', ['sales_invoice', 'pos_sale', 'revenue', 'service_invoice'])
            ->where('journal_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $query = DB::table('journal_entries as je')
            ->leftJoin('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->leftJoin('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->whereIn('je.reference_type', ['sales_invoice', 'pos_sale', 'revenue', 'service_invoice']);

        // Apply filters from request
        if ($request) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $referenceType = $request->input('reference_type');
            $status = $request->input('status');
            $account = $request->input('account');
            $sort = $request->input('sort', 'date');
            $direction = $request->input('direction', 'desc');

            if ($dateFrom) {
                $query->where('je.journal_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->where('je.journal_date', '<=', $dateTo);
            }
            if ($referenceType && $referenceType !== 'all') {
                $query->where('je.reference_type', $referenceType);
            }
            if ($status && $status !== 'all') {
                $query->where('je.status', $status);
            }
            if ($account) {
                $query->havingRaw('GROUP_CONCAT(DISTINCT coa.account_name SEPARATOR ", ") LIKE ?', ["%{$account}%"]);
            }
        }

        $query->select(
            'je.id',
            'je.journal_number',
            DB::raw('je.journal_date as date'),
            'je.reference_type',
            'je.reference_id',
            'je.description',
            DB::raw('je.total_credit as amount'),
            DB::raw('GROUP_CONCAT(DISTINCT coa.account_name SEPARATOR ", ") as account_name'),
            'je.status'
        )
            ->groupBy('je.id', 'je.journal_number', 'je.journal_date', 'je.reference_type', 'je.reference_id', 'je.description', 'je.total_credit', 'je.status');

        // Apply sorting
        if ($request) {
            $sort = $request->input('sort', 'date');
            $direction = $request->input('direction', 'desc');
            $allowedSorts = ['date', 'journal_number', 'reference_type', 'reference_id', 'description', 'amount', 'account_name', 'status'];
            if (in_array($sort, $allowedSorts)) {
                if ($sort === 'date') {
                    $query->orderBy('je.journal_date', $direction);
                } elseif ($sort === 'amount') {
                    $query->orderBy('je.total_credit', $direction);
                } elseif ($sort === 'account_name') {
                    $query->orderBy('account_name', $direction);
                } else {
                    $query->orderBy("je.{$sort}", $direction);
                }
            } else {
                $query->orderBy('je.journal_date', 'desc');
            }
        } else {
            $query->orderBy('je.journal_date', 'desc');
        }

        $transactionsData = $query->get();
        $transactions = ['data' => $transactionsData];

        return [
            'kpi' => [
                'total_revenue' => $revenueCurrent,
                'revenue_growth' => $revenueLast > 0 ? round(($revenueCurrent - $revenueLast) / $revenueLast * 100, 2) : 0,
                'avg_daily_revenue' => $revenueCurrent > 0 ? round($revenueCurrent / now()->daysInMonth, 2) : 0,
            ],
            'by_source' => $revenueBySource,
            'trend' => $revenueTrend,
            'transactions' => $transactions,
        ];
    }

    private function getExpenseAnalysis(Request $request = null)
    {
        $createdBy = creatorId();
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        $expenseCurrent = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->whereIn('je.reference_type', ['expense','sales_invoice_cogs', 'pos_sale_cogs', 'credit_note_cogs', 'pos_return_cogs'])
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->whereMonth('je.journal_date', $currentMonth)
            ->sum('jei.debit_amount');

        $expenseLast = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->whereMonth('je.journal_date', $lastMonth)
            ->sum('jei.debit_amount');

        $expenseByCategory = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->selectRaw('coa.account_name as expense_category, SUM(jei.debit_amount) as total_expense')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->where('jei.debit_amount', '>', 0)
            ->whereMonth('je.journal_date', $currentMonth)
            ->groupBy('coa.id', 'coa.account_name')
            ->orderBy('total_expense', 'desc')
            ->get();

        $expenseTrend = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->selectRaw('DATE_FORMAT(je.journal_date, "%Y-%m") as month, SUM(jei.debit_amount) as total')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->where('je.journal_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Build transaction query with filters (same as revenue pattern)
        $query = DB::table('journal_entries as je')
            ->leftJoin('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->leftJoin('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->leftJoin('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->leftJoin('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->whereIn('je.reference_type', ['expense','sales_invoice_cogs', 'pos_sale_cogs', 'credit_note_cogs', 'pos_return_cogs'])
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->where('jei.debit_amount', '>', 0);

        // Apply filters from request
        if ($request) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $referenceType = $request->input('reference_type');
            $expenseCategory = $request->input('expense_category');
            $status = $request->input('status');
            $sort = $request->input('sort', 'date');
            $direction = $request->input('direction', 'desc');

            if ($dateFrom) {
                $query->where('je.journal_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->where('je.journal_date', '<=', $dateTo);
            }
            if ($referenceType && $referenceType !== 'all') {
                $query->where('je.reference_type', $referenceType);
            }
            if ($expenseCategory && $expenseCategory !== 'all') {
                $query->where('coa.account_name', $expenseCategory);
            }
            if ($status && $status !== 'all') {
                $query->where('je.status', $status);
            }
        }

        $query->select(
            'je.id',
            'je.journal_number',
            DB::raw('je.journal_date as date'),
            'je.reference_type',
            'je.description',
            DB::raw('jei.debit_amount as amount'),
            DB::raw('coa.account_name as expense_account'),
            'je.status'
        )
            ->groupBy('je.id', 'je.journal_number', 'je.journal_date', 'je.reference_type', 'je.description', 'jei.debit_amount', 'coa.account_name', 'je.status');

        // Apply sorting
        if ($request) {
            $sort = $request->input('sort', 'date');
            $direction = $request->input('direction', 'desc');
            $allowedSorts = ['date', 'journal_number', 'reference_type', 'description', 'amount', 'expense_account', 'status'];
            if (in_array($sort, $allowedSorts)) {
                if ($sort === 'date') {
                    $query->orderBy('je.journal_date', $direction);
                } elseif ($sort === 'amount') {
                    $query->orderBy('jei.debit_amount', $direction);
                } elseif ($sort === 'expense_account') {
                    $query->orderBy('expense_account', $direction);
                } else {
                    $query->orderBy("je.{$sort}", $direction);
                }
            } else {
                $query->orderBy('je.journal_date', 'desc');
            }
        } else {
            $query->orderBy('je.journal_date', 'desc');
        }

        $transactionsData = $query->get();
        $transactions = ['data' => $transactionsData];

        return [
            'kpi' => [
                'total_expenses' => $expenseCurrent,
                'expense_growth' => $expenseLast > 0 ? round(($expenseCurrent - $expenseLast) / $expenseLast * 100, 2) : 0,
                'avg_daily_expense' => $expenseCurrent > 0 ? round($expenseCurrent / now()->daysInMonth, 2) : 0,
            ],
            'by_category' => $expenseByCategory,
            'trend' => $expenseTrend,
            'transactions' => $transactions,
        ];
    }

    public function getRevenueTransactions(Request $request = null)
    {
        $createdBy = creatorId();

        $query = DB::table('journal_entries as je')
            ->leftJoin('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->leftJoin('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->whereIn('je.reference_type', ['sales_invoice', 'pos_sale', 'revenue', 'service_invoice']);

        if ($request) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $referenceType = $request->input('reference_type');
            $status = $request->input('status');
            if ($dateFrom) {
                $query->where('je.journal_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->where('je.journal_date', '<=', $dateTo);
            }
            if ($referenceType && $referenceType !== 'all') {
                $query->where('je.reference_type', $referenceType);
            }
            if ($status && $status !== 'all') {
                $query->where('je.status', $status);
            }
        }

        $query->select(
            'je.id',
            'je.journal_number',
            DB::raw('je.journal_date as date'),
            'je.reference_type',
            'je.description',
            DB::raw('je.total_credit as amount'),
            DB::raw('GROUP_CONCAT(DISTINCT coa.account_name SEPARATOR ", ") as account_name'),
            'je.status'
        )
            ->groupBy('je.id', 'je.journal_number', 'je.journal_date', 'je.reference_type', 'je.description', 'je.total_credit', 'je.status');

        if ($request) {
            $sort = $request->input('sort', 'date');
            $direction = $request->input('direction', 'desc');
            $allowedSorts = ['date', 'journal_number', 'reference_type', 'description', 'amount', 'account_name', 'status'];
            if (in_array($sort, $allowedSorts)) {
                if ($sort === 'date') {
                    $query->orderBy('je.journal_date', $direction);
                } elseif ($sort === 'amount') {
                    $query->orderBy('je.total_credit', $direction);
                } elseif ($sort === 'account_name') {
                    $query->orderBy('account_name', $direction);
                } else {
                    $query->orderBy("je.{$sort}", $direction);
                }
            } else {
                $query->orderBy('je.journal_date', 'desc');
            }
        } else {
            $query->orderBy('je.journal_date', 'desc');
        }

        return ['data' => $query->get()];
    }

    public function getExpenseTransactions(Request $request = null)
    {
        $createdBy = creatorId();

        $query = DB::table('journal_entries as je')
            ->leftJoin('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->leftJoin('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->leftJoin('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->leftJoin('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->where('jei.debit_amount', '>', 0);

        if ($request) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $referenceType = $request->input('reference_type');
            $expenseCategory = $request->input('expense_category');
            $status = $request->input('status');
            if ($dateFrom) {
                $query->where('je.journal_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->where('je.journal_date', '<=', $dateTo);
            }
            if ($referenceType && $referenceType !== 'all') {
                $query->where('je.reference_type', $referenceType);
            }
            if ($expenseCategory && $expenseCategory !== 'all') {
                $query->where('coa.account_name', $expenseCategory);
            }
            if ($status && $status !== 'all') {
                $query->where('je.status', $status);
            }
        }

        $query->select(
            'je.id',
            'je.journal_number',
            DB::raw('je.journal_date as date'),
            'je.reference_type',
            'je.description',
            DB::raw('jei.debit_amount as amount'),
            DB::raw('coa.account_name as expense_account'),
            'je.status'
        )
            ->groupBy('je.id', 'je.journal_number', 'je.journal_date', 'je.reference_type', 'je.description', 'jei.debit_amount', 'coa.account_name', 'je.status');

        if ($request) {
            $sort = $request->input('sort', 'date');
            $direction = $request->input('direction', 'desc');
            $allowedSorts = ['date', 'journal_number', 'reference_type', 'description', 'amount', 'expense_account', 'status'];
            if (in_array($sort, $allowedSorts)) {
                if ($sort === 'date') {
                    $query->orderBy('je.journal_date', $direction);
                } elseif ($sort === 'amount') {
                    $query->orderBy('jei.debit_amount', $direction);
                } elseif ($sort === 'expense_account') {
                    $query->orderBy('expense_account', $direction);
                } else {
                    $query->orderBy("je.{$sort}", $direction);
                }
            } else {
                $query->orderBy('je.journal_date', 'desc');
            }
        } else {
            $query->orderBy('je.journal_date', 'desc');
        }

        return ['data' => $query->get()];
    }

    private function getProfitability()
    {
        $createdBy = creatorId();

        $totalRevenue = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'revenue')
            ->whereMonth('je.journal_date', now()->month)
            ->sum('jei.credit_amount');

        $totalExpense = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->whereMonth('je.journal_date', now()->month)
            ->sum('jei.debit_amount');

        $netProfit = round($totalRevenue - $totalExpense, 2);
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;

        // Profit trend - Revenue (Only revenue type transactions)
        $revenueTrendData = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'revenue')
            ->whereIn('je.reference_type', ['sales_invoice', 'service_invoice', 'pos_sale', 'revenue','project_payment'])
            ->where('je.journal_date', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(je.journal_date, "%Y-%m") as month, SUM(jei.credit_amount) as revenue')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Profit trend - Expense (Only expense type transactions)
        $expenseTrendData = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->whereIn('je.reference_type', ['expense','sales_invoice_cogs', 'pos_sale_cogs', 'credit_note_cogs', 'pos_return_cogs'])
            ->where('je.journal_date', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(je.journal_date, "%Y-%m") as month, SUM(jei.debit_amount) as expense')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $allMonthKeys = array_unique(array_merge(
            $revenueTrendData->keys()->toArray(),
            $expenseTrendData->keys()->toArray()
        ));
        sort($allMonthKeys);

        $profitTrend = collect($allMonthKeys)->map(function ($month) use ($revenueTrendData, $expenseTrendData) {
            $revenue = (float) ($revenueTrendData[$month]->revenue ?? 0);
            $expense = (float) ($expenseTrendData[$month]->expense ?? 0);
            return [
                'month' => $month,
                'revenue' => $revenue,
                'expense' => $expense,
                'profit' => $revenue - $expense,
            ];
        });

        // By transaction type - Revenue
        $revenueByType = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'revenue')
            ->whereMonth('je.journal_date', now()->month)
            ->selectRaw('je.reference_type, SUM(jei.credit_amount) as revenue')
            ->groupBy('je.reference_type')
            ->get()
            ->keyBy('reference_type');

        // By transaction type - Expense
        $expenseByType = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->whereMonth('je.journal_date', now()->month)
            ->selectRaw('je.reference_type, SUM(jei.debit_amount) as cost')
            ->groupBy('je.reference_type')
            ->get()
            ->keyBy('reference_type');

        $allTypes = $revenueByType->keys()
            ->merge($expenseByType->keys())
            ->unique()
            ->values();

        $byTransactionType = $allTypes->map(function ($type) use ($revenueByType, $expenseByType) {
            $revenue = $revenueByType[$type]->revenue ?? 0;
            $cost = $expenseByType[$type]->cost ?? 0;
            $profit = $revenue - $cost;
            return [
                'transaction_type' => $type,
                'revenue' => $revenue,
                'cost' => $cost,
                'profit' => $profit,
                'margin' => $revenue > 0 ? round(($profit / $revenue) * 100, 2) : 0,
            ];
        });

        // Waterfall data: Revenue → Expenses → Profit
        $waterfall = [
            ['label' => __('Revenue'), 'value' => round((float) $totalRevenue, 2), 'type' => 'positive'],
            ['label' => __('Expenses'), 'value' => round((float) $totalExpense, 2), 'type' => 'negative'],
            ['label' => __('Net Profit'), 'value' => round((float) $netProfit, 2), 'type' => $netProfit >= 0 ? 'positive' : 'negative'],
        ];

        // By account breakdown (revenue + expense accounts for current month)
        $byAccount = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', '!=', 'assets')
            ->where('ac.type', '!=', 'liabilities')
            ->whereMonth('je.journal_date', now()->month)
            ->select(
                'coa.account_name',
                'coa.account_code',
                'ac.type as account_type',
                DB::raw('SUM(COALESCE(jei.debit_amount, 0) - COALESCE(jei.credit_amount, 0)) as amount')
            )
            ->groupBy('coa.id', 'coa.account_name', 'coa.account_code', 'ac.type')
            ->orderBy('ac.type')
            ->orderBy('amount', 'desc')
            ->get();

        // By month breakdown (revenue, expense, profit per month for last 12 months)
        $byMonthRevenue = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'revenue')
            ->whereIn('je.reference_type', [
                'sales_invoice', 'service_invoice', 'pos_sale', 'revenue',
                'mobile_service_payment', 'fleet_booking_payment', 'beauty_booking_payment',
                'dairy_cattle_payment', 'catering_order_payment', 'event_booking_payment',
                'hotel_booking_payment', 'parking_booking_payment', 'vehicle_booking_payment',
                'laundry_payment', 'medical_order_payment', 'fee_receive', 'membership_plan_payment',
                'project_payment'
            ])
            ->where('je.journal_date', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(je.journal_date, "%Y-%m") as month, SUM(jei.credit_amount) as revenue')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $byMonthExpense = DB::table('journal_entries as je')
            ->join('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('ac.type', 'expenses')
            ->whereIn('je.reference_type', [
                'expense', 'payroll', 'commission_payment', 'dairy_cattle_expense_tracking',
                'catering_expense_tracking', 'fleet_expense', 'case_expense', 'laundry_expense',
                'sales_invoice_cogs', 'pos_sale_cogs', 'credit_note_cogs', 'pos_return_cogs'
            ])
            ->where('je.journal_date', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(je.journal_date, "%Y-%m") as month, SUM(jei.debit_amount) as expense')
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $byMonthKeys = array_unique(array_merge(
            $byMonthRevenue->keys()->toArray(),
            $byMonthExpense->keys()->toArray()
        ));
        sort($byMonthKeys);

        $byMonth = collect($byMonthKeys)->map(function ($month) use ($byMonthRevenue, $byMonthExpense) {
            $revenue = (float) ($byMonthRevenue[$month]->revenue ?? 0);
            $expense = (float) ($byMonthExpense[$month]->expense ?? 0);
            return [
                'month' => $month,
                'revenue' => $revenue,
                'expense' => $expense,
                'profit' => $revenue - $expense,
            ];
        });

        return [
            'summary' => [
                'gross_revenue' => round((float) $totalRevenue, 2),
                'total_expenses' => round((float) $totalExpense, 2),
                'net_profit' => $netProfit,
                'profit_margin' => $profitMargin,
            ],
            'trend' => $profitTrend,
            'by_transaction_type' => $byTransactionType,
            'waterfall' => $waterfall,
            'by_account' => $byAccount,
            'by_month' => $byMonth,
        ];
    }

    private function getCashFlow()
    {
        $createdBy = creatorId();

        $cashBalance = DB::table('chart_of_accounts as coa')
            ->join('account_types as at', 'coa.account_type_id', '=', 'at.id')
            ->join('account_categories as ac', 'at.category_id', '=', 'ac.id')
            ->where('coa.created_by', $createdBy)
            ->where('ac.type', 'assets')
            ->where('coa.account_code', 'LIKE', '10%')
            ->sum('coa.current_balance') ?? 0;

        $accountsReceivable = DB::table('sales_invoices')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['posted', 'partial'])
            ->sum('balance_amount');

        $accountsPayable = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['posted', 'partial'])
            ->sum('balance_amount');

        $cashFlowForecast = DB::table('journal_entries as je')
            ->leftJoin('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->leftJoin('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->where('je.created_by', $createdBy)
            ->where('je.status', 'posted')
            ->where('je.journal_date', '>=', now()->subDays(30))
            ->where('je.journal_date', '<=', now()->addDays(90))
            ->selectRaw('je.journal_date, SUM(COALESCE(jei.debit_amount, 0) - COALESCE(jei.credit_amount, 0)) as net_cash_flow')
            ->groupBy('je.journal_date')
            ->orderBy('je.journal_date')
            ->where('coa.account_name', 'Cash')
            ->get();

        // AR Aging - Per Customer Breakdown
        $customers = DB::table('users')
            ->where('created_by', $createdBy)
            ->where('type', 'client')
            ->select('id', 'name', 'email')
            ->get();

        $arAging = collect();
        foreach ($customers as $customer) {
            $invoiced = DB::table('sales_invoices')
                ->where('customer_id', $customer->id)
                ->whereIn('status', ['posted', 'partial', 'paid'])
                ->sum('total_amount');

            $returns = DB::table('credit_notes')
                ->where('customer_id', $customer->id)
                ->whereIn('status', ['approved', 'applied'])
                ->sum('total_amount');

            $balance = DB::table('sales_invoices')
                ->where('customer_id', $customer->id)
                ->whereIn('status', ['posted', 'partial', 'paid'])
                ->sum('balance_amount');

            $netInvoiced = $invoiced - $returns;
            $paid = $invoiced - $balance;

            if ($balance > 0) {
                $dueDate = DB::table('sales_invoices')
                    ->where('customer_id', $customer->id)
                    ->whereIn('status', ['posted', 'partial'])
                    ->max('due_date');

                $daysOverdue = DB::raw('COALESCE(DATEDIFF(CURDATE(), "' . ($dueDate ?? date('Y-m-d')) . '"), 0)');

                $hasPartial = DB::table('sales_invoices')
                    ->where('customer_id', $customer->id)
                    ->whereIn('status', ['posted', 'partial', 'paid'])
                    ->where('status', 'partial')
                    ->exists();

                $status = $balance <= 0 ? 'paid' : ($hasPartial ? 'partial' : 'outstanding');

                $arAging->push((object) [
                    'customer_id' => $customer->id,
                    'customer' => $customer->name,
                    'customer_email' => $customer->email,
                    'total_invoiced' => $invoiced,
                    'total_returns' => $returns,
                    'net_invoiced' => $netInvoiced,
                    'total_paid' => $paid,
                    'balance' => $balance,
                    'due_date' => $dueDate ?? date('Y-m-d'),
                    'days_overdue' => $dueDate ? max(0, (int) now()->diffInDays(Carbon::parse($dueDate), false)) : 0,
                    'status' => $status,
                ]);
            }
        }

        $arAging = $arAging->sortByDesc('balance')->values();

        // AP Aging
        $apAging = DB::table('purchase_invoices')
            ->leftJoin('users', 'purchase_invoices.vendor_id', '=', 'users.id')
            ->where('purchase_invoices.created_by', $createdBy)
            ->whereIn('purchase_invoices.status', ['posted', 'partial'])
            ->select('purchase_invoices.id', 'purchase_invoices.vendor_id as vendor_id', 'users.name as vendor', 'purchase_invoices.invoice_number', 'purchase_invoices.total_amount as amount', 'purchase_invoices.due_date', 'purchase_invoices.status', DB::raw('COALESCE(DATEDIFF(purchase_invoices.due_date, CURDATE()), 0) as days_until_due'))
            ->orderBy('days_until_due', 'asc')
            ->limit(50)
            ->get();

        return [
            'kpi' => [
                'cash_balance' => $cashBalance,
                'accounts_receivable' => $accountsReceivable,
                'accounts_payable' => $accountsPayable,
            ],
            'forecast' => $cashFlowForecast,
            'ar_aging' => $arAging,
            'ap_aging' => $apAging,
        ];
    }

    private function getJournalEntries()
    {
        $createdBy = creatorId();

        $entries = DB::table('journal_entries')
            ->where('created_by', $createdBy)
            ->select(
                'id',
                'journal_number',
                'journal_date as date',
                'entry_type',
                'reference_type',
                'reference_id',
                'description',
                'total_debit as debit',
                'total_credit as credit',
                'status'
            )
            ->orderBy('journal_date', 'desc')
            ->get();

        $entries->each(function ($entry) use ($createdBy) {
            $items = DB::table('journal_entry_items as jei')
                ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
                ->where('jei.journal_entry_id', $entry->id)
                ->select(
                    'coa.account_name',
                    'coa.account_code',
                    DB::raw('COALESCE(jei.debit_amount, 0) as debit_amount'),
                    DB::raw('COALESCE(jei.credit_amount, 0) as credit_amount')
                )
                ->get();

            $entry->items = $items;
        });

        return ['data' => $entries];
    }

    public function getJournalEntriesFiltered(Request $request = null)
    {
        $createdBy = creatorId();

        $query = DB::table('journal_entries as je')
            ->leftJoin('journal_entry_items as jei', 'je.id', '=', 'jei.journal_entry_id')
            ->leftJoin('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
            ->whereIn('je.reference_type', ['sales_invoice', 'pos_sale', 'revenue', 'service_invoice', 'expense','sales_invoice_cogs', 'pos_sale_cogs', 'credit_note_cogs', 'pos_return_cogs'])
            ->where('je.created_by', $createdBy);

        if ($request) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $entryType = $request->input('entry_type');
            $referenceType = $request->input('reference_type');
            $status = $request->input('status');
            $sort = $request->input('sort', 'date');
            $direction = $request->input('direction', 'desc');

            if ($dateFrom) {
                $query->where('je.journal_date', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->where('je.journal_date', '<=', $dateTo);
            }
            if ($entryType && $entryType !== 'all') {
                $query->where('je.entry_type', $entryType);
            }
            if ($referenceType && $referenceType !== 'all') {
                $query->where('je.reference_type', $referenceType);
            }
            if ($status && $status !== 'all') {
                $query->where('je.status', $status);
            }
        }

        $query->select(
            'je.id',
            'je.journal_number',
            DB::raw('je.journal_date as date'),
            'je.entry_type',
            'je.reference_type',
            DB::raw('GROUP_CONCAT(DISTINCT CONCAT(coa.account_code, " - ", coa.account_name) SEPARATOR ", ") as accounts'),
            'je.description',
            DB::raw('SUM(COALESCE(jei.debit_amount, 0)) as debit'),
            DB::raw('SUM(COALESCE(jei.credit_amount, 0)) as credit'),
            'je.status'
        )
            ->groupBy('je.id', 'je.journal_number', 'je.journal_date', 'je.entry_type', 'je.reference_type', 'je.description', 'je.status');

        if ($request) {
            $sort = $request->input('sort', 'date');
            $direction = $request->input('direction', 'desc');
            $allowedSorts = ['date', 'journal_number', 'entry_type', 'reference_type', 'accounts', 'description', 'debit', 'credit', 'status'];
            if (in_array($sort, $allowedSorts)) {
                if ($sort === 'date') {
                    $query->orderBy('je.journal_date', $direction);
                } elseif ($sort === 'accounts') {
                    $query->orderBy('accounts', $direction);
                } else {
                    $query->orderBy("je.{$sort}", $direction);
                }
            } else {
                $query->orderBy('je.journal_date', 'desc');
            }
        } else {
            $query->orderBy('je.journal_date', 'desc');
        }

        $entries = $query->get();

        $entries->each(function ($entry) use ($createdBy) {
            $items = DB::table('journal_entry_items as jei')
                ->join('chart_of_accounts as coa', 'jei.account_id', '=', 'coa.id')
                ->where('jei.journal_entry_id', $entry->id)
                ->select(
                    'coa.account_name',
                    'coa.account_code',
                    DB::raw('COALESCE(jei.debit_amount, 0) as debit_amount'),
                    DB::raw('COALESCE(jei.credit_amount, 0) as credit_amount')
                )
                ->get();

            $entry->items = $items;
        });

        return ['data' => $entries];
    }
}
