import React, { useState } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { formatCurrency, formatDate } from '@/utils/helpers';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { DollarSign, Users, Briefcase, CalendarCheck, TrendingUp, TrendingDown, Activity, Target, AlertTriangle, CheckCircle, XCircle, Clock, ShoppingCart, Package, UserCheck, ChevronDown, ChevronUp, ChevronLeft, ChevronRight } from 'lucide-react';
import ExportButton from '../../Components/ExportButton';

interface KpiCard {
    revenue: { current: number; previous: number; growth: number; trend: Record<string, number> };
    profit: { revenue: number; expenses: number; net: number; margin: number };
    employees: { active: number; new_hires: number; attrition_rate: number };
    projects: { active: number; completed: number; on_hold: number };
    attendance: { today: number; monthly_average: number };
    sales_pipeline: { active_leads: number; pipeline_value: number; conversion_rate: number };
}

interface PosTopProduct {
    product_name: string;
    units_sold: number;
    revenue: number;
}

interface ModuleSummary {
    hrm: { total_employees: number; today_attendance: number; pending_leaves: number };
    crm: { active_leads: number; conversion_rate: number; pipeline_value: number };
    account: { cash_balance: number; pending_invoices: number; pending_bills: number };
    pos: { today_sales: number; today_transactions: number; top_products: PosTopProduct[] };
    projects: {
        active_projects: number;
        completion_rate: number;
        total_budget: number;
        total_milestone_cost: number;
        budget_status_pct: number;
        budget_remaining: number;
    };
    sales: {
        monthly_revenue: number;
        quotations_count: number;
        quotations_value: number;
        orders_count: number;
        orders_value: number;
    };
    inventory: {
        total_stock_value: number;
        total_stock_units: number;
        low_stock_items: number;
        out_of_stock: number;
    };
}

interface EmployeeItem {
    id: string;
    employee_id: string;
    employee_name: string;
    department_name: string | null;
    designation_name: string | null;
    tasks_completed: number;
    attendance_percentage: number;
    productivity_score: number;
}

interface CustomerItem {
    customer_id: number;
    customer_name: string;
    total_orders: number;
    gross_revenue: number;
    returns_total: number;
    credits_applied: number;
    net_revenue: number;
    invoice_paid_amount: number;
    total_payments: number;
    total_paid: number;
    outstanding: number;
    last_purchase_date: string;
    average_order_value: number;
    status: 'Active' | 'Inactive';
}

interface TransactionItem {
    date: string;
    type: string;
    module: string;
    amount: number;
    status: string;
}

interface QuickInsight {
    type: 'critical' | 'warning' | 'info' | 'positive';
    title: string;
    message: string;
    module: string;
}

interface ExecutiveDashboardProps extends Record<string, any> {
    kpi_cards: KpiCard;
    quick_insights: QuickInsight[];
    module_summaries: ModuleSummary;
    top_employees: EmployeeItem[];
    top_customers: CustomerItem[];
    recent_transactions: TransactionItem[];
}

export default function ExecutiveDashboard() {
    const { t } = useTranslation();
    const { kpi_cards, quick_insights, module_summaries, top_employees, top_customers, recent_transactions } =
        usePage<ExecutiveDashboardProps>().props;
    const page = usePage<ExecutiveDashboardProps>();
    const pageProps = page.props as any;

    const getInsightIcon = (type: QuickInsight['type']) => {
        switch (type) {
            case 'critical': return <XCircle className="h-5 w-5" />;
            case 'warning': return <AlertTriangle className="h-5 w-5" />;
            case 'info': return <Activity className="h-5 w-5" />;
            case 'positive': return <CheckCircle className="h-5 w-5" />;
        }
    };

    const getInsightStyles = (type: QuickInsight['type']) => {
        switch (type) {
            case 'critical': return { border: 'border-red-200 dark:border-red-900', bg: 'bg-red-50 dark:bg-red-950/20', icon: 'text-red-600' };
            case 'warning': return { border: 'border-yellow-200 dark:border-yellow-900', bg: 'bg-yellow-50 dark:bg-yellow-950/20', icon: 'text-yellow-600' };
            case 'info': return { border: 'border-blue-200 dark:border-blue-900', bg: 'bg-blue-50 dark:bg-blue-950/20', icon: 'text-blue-600' };
            case 'positive': return { border: 'border-green-200 dark:border-green-900', bg: 'bg-green-50 dark:bg-green-950/20', icon: 'text-green-600' };
        }
    };

    const formattedCustomers = (top_customers || []).map(cust => ({
        net_revenue: formatCurrency(cust.net_revenue || 0),
        gross_revenue: formatCurrency(cust.gross_revenue || 0),
        returns_total: formatCurrency(cust.returns_total || 0),
        credits_applied: formatCurrency(cust.credits_applied || 0),
        total_paid: formatCurrency(cust.total_paid || 0),
        outstanding: formatCurrency(cust.outstanding || 0),
        average_order_value: formatCurrency(cust.average_order_value || 0),
    }));

    const formattedTransactions = (recent_transactions || []).map(txn => ({
        ...txn,
        formatted_amount: formatCurrency(txn.amount || 0),
    }));

    const getTypeBadgeStyles = (type: string) => {
        switch (type) {
            case 'Revenue': return 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800';
            case 'POS Sale': return 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800';
            case 'Expense': return 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800';
            default: return 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700';
        }
    };

    const getCustomerStatusStyles = (status: string) => {
        switch (status) {
            case 'Active': return 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800';
            case 'Inactive': return 'bg-red-100 text-red-800 border-red-200 dark:bg-red-800 dark:text-red-300 dark:border-red-700';
            default: return 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700';
        }
    };



    const getInsightBadgeStyles = (type: QuickInsight['type']) => {
        switch (type) {
            case 'critical': return 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800';
            case 'warning': return 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800';
            case 'positive': return 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800';
            case 'info': return 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800';
            default: return 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700';
        }
    };

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'posted': return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800';
            case 'paid': return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800';
            case 'completed': return 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800';
            case 'pending': return 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800';
            case 'cancelled': return 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800';
            default: return 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700';
        }
    };

    const [expandedEmployee, setExpandedEmployee] = useState<number | null>(null);
    const [expandedCustomer, setExpandedCustomer] = useState<number | null>(null);
    const [activeDataTab, setActiveDataTab] = useState<'employees' | 'customers' | 'transactions'>('employees');
    const [transactionsPage, setTransactionsPage] = useState(1);
    const [transactionsPerPage, setTransactionsPerPage] = useState('10');
    const transactionsPerPageNum = parseInt(transactionsPerPage, 10);
    const totalTransactionsPages = Math.ceil((recent_transactions || []).length / transactionsPerPageNum);
    const paginatedTransactions = (formattedTransactions || []).slice(
        (transactionsPage - 1) * transactionsPerPageNum,
        transactionsPage * transactionsPerPageNum
    );

    const employeeExportColumns = [
        { key: 'employee_name', header: 'Name' },
        { key: 'employee_id', header: 'Employee ID' },
        { key: 'department_name', header: 'Department' },
        { key: 'designation_name', header: 'Designation' },
        { key: 'tasks_completed', header: 'Tasks Completed' },
        { key: 'attendance_percentage', header: 'Attendance %', render: (v: number) => `${v || 0}%` },
        { key: 'productivity_score', header: 'Productivity Score', render: (v: number) => `${Math.round(v || 0)}` },
    ];

    const customerExportColumns = [
        { key: 'customer_name', header: 'Customer Name' },
        { key: 'total_orders', header: 'Total Orders' },
        { key: 'gross_revenue', header: 'Gross Revenue', render: (v: number) => formatCurrency(v || 0) },
        { key: 'returns_total', header: 'Returns Total', render: (v: number) => formatCurrency(v || 0) },
        { key: 'credits_applied', header: 'Credits Applied', render: (v: number) => formatCurrency(v || 0) },
        { key: 'net_revenue', header: 'Net Revenue', render: (v: number) => formatCurrency(v || 0) },
        { key: 'total_paid', header: 'Total Paid', render: (v: number) => formatCurrency(v || 0) },
        { key: 'outstanding', header: 'Outstanding', render: (v: number) => formatCurrency(v || 0) },
        { key: 'last_purchase_date', header: 'Last Purchase' },
        { key: 'average_order_value', header: 'Avg Order Value', render: (v: number) => formatCurrency(v || 0) },
        { key: 'status', header: 'Status' },
    ];

    const transactionExportColumns = [
        { key: 'date', header: 'Date' },
        { key: 'type', header: 'Type' },
        { key: 'module', header: 'Module' },
        { key: 'amount', header: 'Amount', render: (v: number) => formatCurrency(v || 0) },
        { key: 'status', header: 'Status' },
    ];

    const toggleEmployeeExpand = (idx: number) => {
        setExpandedEmployee(expandedEmployee === idx ? null : idx);
    };

    const toggleCustomerExpand = (idx: number) => {
        setExpandedCustomer(expandedCustomer === idx ? null : idx);
    };

    const getTabButtonClasses = (tab: string) =>
        activeDataTab === tab
            ? 'border-b-2 border-primary text-primary font-semibold'
            : 'text-muted-foreground hover:text-foreground';

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Smart Dashboard') },
                { label: t('Executive Overview') }
            ]}
            pageTitle={t('Executive Overview')}
        >
            <Head title={t('Executive Overview')} />

            {/* Row 1: Revenue + Profit + Employees Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle className="text-sm font-medium">{t('Monthly Revenue')}</CardTitle>
                        <DollarSign className="h-4 w-4 flex-shrink-0 text-purple-600" />
                    </CardHeader>
                    <CardContent>
                        <div className="text-2xl font-bold">{formatCurrency(kpi_cards.revenue.current || 0)}</div>
                        <div className="flex items-center gap-1 text-xs mt-1">
                            {kpi_cards.revenue.growth >= 0 ? (
                                <TrendingUp className="h-3 w-3 text-green-600 flex-shrink-0" />
                            ) : (
                                <TrendingDown className="h-3 w-3 text-red-600 flex-shrink-0" />
                            )}
                            <span className={kpi_cards.revenue.growth >= 0 ? 'text-green-600' : 'text-red-600'}>
                                {kpi_cards.revenue.growth}% {t('vs last month')}
                            </span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle className="text-sm font-medium">{t('Monthly Revenue & Expense Summary')}</CardTitle>
                        <TrendingUp className="h-4 w-4 text-green-600 flex-shrink-0" />
                    </CardHeader>
                    <CardContent>
                        <div className="text-2xl font-bold">{formatCurrency(kpi_cards.profit.net || 0)}</div>
                        <div className="flex items-center gap-1 text-xs mt-1">
                            <span className={kpi_cards.profit.margin >= 15 ? 'text-green-600' : 'text-red-600'}>
                                {kpi_cards.profit.margin}% {t('margin')}
                            </span>
                            {kpi_cards.profit.margin < 15 && <AlertTriangle className="h-3 w-3 text-red-600 flex-shrink-0" />}
                        </div>
                    </CardContent>
                </Card>

                {/* Active Employees */}
                <Card className="shadow-sm">
                    <CardHeader className="flex flex-row items-center justify-between px-4 pt-3 pb-2">
                        <CardTitle className="text-sm font-semibold">
                            {t('Total Employees')}
                        </CardTitle>
                        <Users className="h-4 w-4 text-blue-600" />
                    </CardHeader>
                    <CardContent className="px-4 pb-3">
                        <div className="text-xl font-bold">
                            {kpi_cards.employees.active || 0}
                        </div>
                        <div className="flex flex-wrap gap-x-2 gap-y-1 text-xs mt-1.5">
                            <span className="text-green-600 font-medium">
                                +{kpi_cards.employees.new_hires} {t('new hires')}
                            </span>
                            <span
                                className={
                                    kpi_cards.employees.attrition_rate > 5
                                        ? 'text-red-600 font-medium'
                                        : 'text-muted-foreground'
                                }
                            >
                                {kpi_cards.employees.attrition_rate}% {t('attrition')}
                            </span>
                        </div>
                    </CardContent>
                </Card>

                {/* Projects */}
                <Card className="shadow-sm">
                    <CardHeader className="flex flex-row items-center justify-between px-4 pt-3 pb-2">
                        <CardTitle className="text-sm font-semibold">
                            {t('Total Projects')}
                        </CardTitle>
                        <Briefcase className="h-4 w-4 text-cyan-600" />
                    </CardHeader>
                    <CardContent className="px-4 pb-3">
                        <div className="text-xl font-bold">
                            {kpi_cards.projects.active || 0}
                        </div>
                        <div className="text-xs text-muted-foreground mt-1.5">
                            {kpi_cards.projects.completed} {t('completed')} ·{' '}
                            {kpi_cards.projects.on_hold} {t('on hold')}
                        </div>
                    </CardContent>
                </Card>
                {/* Attendance */}
                <Card className="shadow-sm">
                    <CardHeader className="flex flex-row items-center justify-between px-4 pt-3 pb-2">
                        <CardTitle className="text-sm font-semibold">
                            {t('Today Attendance')}
                        </CardTitle>
                        <CalendarCheck className="h-4 w-4 text-rose-600" />
                    </CardHeader>
                    <CardContent className="px-4 pb-3">
                        <div className="text-xl font-bold">
                            {kpi_cards.attendance.today || 0}%
                        </div>
                        <div className="flex flex-wrap gap-x-2 gap-y-1 text-xs mt-1.5">
                            <span
                                className={
                                    kpi_cards.attendance.today >= 80
                                        ? 'text-green-600 font-medium'
                                        : kpi_cards.attendance.today >= 50
                                            ? 'text-yellow-600 font-medium'
                                            : 'text-red-600 font-medium'
                                }
                            >
                                {t("Today's")} {kpi_cards.attendance.today || 0}%
                            </span>
                            <span className="text-muted-foreground">
                                {t('Monthly avg')} {kpi_cards.attendance.monthly_average || 0}%
                            </span>
                        </div>
                    </CardContent>
                </Card>
                {/* Sales Pipeline */}
                <Card className="shadow-sm">
                    <CardHeader className="flex flex-row items-center justify-between px-4 pt-3 pb-2">
                        <CardTitle className="text-sm font-semibold">
                            {t('Sales Pipeline')}
                        </CardTitle>
                        <Target className="h-4 w-4 text-indigo-600" />
                    </CardHeader>
                    <CardContent className="px-4 pb-3">
                        <div className="text-lg font-bold">
                            {formatCurrency(
                                kpi_cards.sales_pipeline.pipeline_value || 0
                            )}
                        </div>
                        <div className="flex flex-wrap gap-x-2 gap-y-1 text-xs mt-1.5">
                            <span className="text-blue-600 font-medium">
                                {kpi_cards.sales_pipeline.active_leads} {t('leads')}
                            </span>
                            <span
                                className={
                                    kpi_cards.sales_pipeline.conversion_rate >= 20
                                        ? 'text-green-600 font-medium'
                                        : 'text-yellow-600 font-medium'
                                }
                            >
                                {kpi_cards.sales_pipeline.conversion_rate}% {t('conversion')}
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Quick Insights Panel */}
            <div className="mb-4">
                <h2 className="text-base font-semibold mb-3">
                    {t('Quick Insights')}
                </h2>
                {quick_insights && quick_insights.length > 0 ? (
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        {quick_insights.map((insight, idx) => {
                            const styles = getInsightStyles(insight.type);
                            return (
                                <Card
                                    key={idx}
                                    className={`${styles.border} ${styles.bg} border-l-4 shadow-sm hover:shadow-md transition-all`}
                                >
                                    <CardContent className="p-3">
                                        <div className="flex items-start gap-2.5">
                                            {/* Icon */}
                                            <div className={`shrink-0 mt-0.5 ${styles.icon}`}>
                                                {getInsightIcon(insight.type)}
                                            </div>
                                            {/* Content */}
                                            <div className="flex-1 min-w-0">
                                                <div className="flex items-start justify-between gap-2 mb-1">
                                                    <h4 className="text-sm font-semibold leading-5">
                                                        {insight.title}
                                                    </h4>
                                                    <span
                                                        className={`px-1.5 py-0.5 rounded-full text-[10px] font-medium border whitespace-nowrap ${getInsightBadgeStyles(
                                                            insight.type
                                                        )}`}
                                                    >
                                                        {t(
                                                            insight.type === 'critical'
                                                                ? 'Critical'
                                                                : insight.type === 'warning'
                                                                    ? 'Warning'
                                                                    : insight.type === 'positive'
                                                                        ? 'Positive'
                                                                        : 'Info'
                                                        )}
                                                    </span>
                                                </div>
                                                <p className="text-xs text-muted-foreground leading-5 line-clamp-3">
                                                    {insight.message}
                                                </p>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            );
                        })}
                    </div>
                ) : (
                    <Card className="shadow-sm">
                        <CardContent className="py-6 text-center">
                            <CheckCircle className="h-7 w-7 mx-auto mb-2 text-green-500" />
                            <p className="text-sm text-muted-foreground">
                                {t(
                                    'All systems are running smoothly. No critical issues detected.'
                                )}
                            </p>
                        </CardContent>
                    </Card>
                )}
            </div>

            {/* Multi-Module Summary Grid */}
            <div className="mb-4">
                <h2 className="text-base font-semibold mb-3">
                    {t('Module Summaries')}
                </h2>
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    {/* HRM */}
                    <Card className="shadow-sm">
                        <CardHeader className="px-4 pt-3 pb-2">
                            <CardTitle className="text-sm font-semibold flex items-center gap-2">
                                <Users className="h-4 w-4 text-blue-600" />
                                {t('HRM')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="px-4 pb-3 space-y-2">
                            <div className="flex justify-between items-center">
                                <span className="text-xs text-muted-foreground">
                                    {t('Total Employees')}
                                </span>
                                <span className="text-sm font-semibold">
                                    {module_summaries.hrm.total_employees}
                                </span>
                            </div>
                            <div className="flex justify-between items-center">
                                <span className="text-xs text-muted-foreground">
                                    {t("Today's Attendance")}
                                </span>
                                <span className="text-sm font-semibold">
                                    {module_summaries.hrm.today_attendance}
                                </span>
                            </div>
                            <div className="flex justify-between items-center">
                                <span className="text-xs text-muted-foreground">
                                    {t('Pending Leaves')}
                                </span>
                                <span className="text-sm font-semibold text-yellow-600">
                                    {module_summaries.hrm.pending_leaves}
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                    {/* CRM */}
                    <Card className="shadow-sm">
                        <CardHeader className="px-4 pt-3 pb-2">
                            <CardTitle className="text-sm font-semibold flex items-center gap-2">
                                <Target className="h-4 w-4 text-purple-600" />
                                {t('CRM')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="px-4 pb-3 space-y-2">
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Active Leads')}
                                </span>
                                <span className="text-sm font-semibold">
                                    {module_summaries.crm.active_leads}
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Conversion Rate')}
                                </span>
                                <span className="text-sm font-semibold">
                                    {module_summaries.crm.conversion_rate}%
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Pipeline Value')}
                                </span>
                                <span className="text-sm font-semibold whitespace-nowrap">
                                    {formatCurrency(module_summaries.crm.pipeline_value || 0)}
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                    {/* Account */}
                    <Card className="shadow-sm">
                        <CardHeader className="px-4 pt-3 pb-2">
                            <CardTitle className="text-sm font-semibold flex items-center gap-2">
                                <DollarSign className="h-4 w-4 text-green-600" />
                                {t('Account')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="px-4 pb-3 space-y-2">
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Cash Balance')}
                                </span>
                                <span className="text-sm font-semibold whitespace-nowrap">
                                    {formatCurrency(module_summaries.account.cash_balance || 0)}
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Pending Invoices')}
                                </span>
                                <span className="text-sm font-semibold text-yellow-600">
                                    {module_summaries.account.pending_invoices}
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Pending Bills')}
                                </span>
                                <span className="text-sm font-semibold text-orange-600">
                                    {module_summaries.account.pending_bills}
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                    {/* POS */}
                    <Card className="shadow-sm">
                        <CardHeader className="px-4 pt-3 pb-2">
                            <CardTitle className="text-sm font-semibold flex items-center gap-2">
                                <ShoppingCart className="h-4 w-4 text-indigo-600" />
                                {t('POS')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="px-4 pb-3 space-y-2">
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t("Today's Sales")}
                                </span>
                                <span className="text-sm font-semibold">
                                    {formatCurrency(module_summaries.pos.today_sales || 0)}
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Transactions')}
                                </span>
                                <span className="text-sm font-semibold">
                                    {module_summaries.pos.today_transactions}
                                </span>
                            </div>
                            <span className="text-sm font-semibold">
                                {module_summaries.pos.top_products && module_summaries.pos.top_products.length > 0 && (
                                    <div className="">
                                        <p className="text-xs font-medium text-muted-foreground mb-1">{t('Top Products')}</p>
                                        {module_summaries.pos.top_products.map((prod, idx) => (
                                            <div key={idx} className="flex justify-between text-xs">
                                                <span className="mr-1">{prod.product_name}</span>
                                                <span className="font-medium">{prod.units_sold} {t('units')}</span>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </span>
                        </CardContent>
                    </Card>
                    {/* Projects */}
                    <Card className="shadow-sm">
                        <CardHeader className="px-4 pt-3 pb-2">
                            <CardTitle className="text-sm font-semibold flex items-center gap-2">
                                <Briefcase className="h-4 w-4 text-cyan-600" />
                                {t('Projects')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="px-4 pb-3 space-y-2">
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Active Projects')}
                                </span>
                                <span className="text-sm font-semibold">
                                    {module_summaries.projects.active_projects}
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Completion Rate')}
                                </span>
                                <span className="text-sm font-semibold">
                                    {module_summaries.projects.completion_rate}%
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Total Budget')}
                                </span>
                                <span className="text-sm font-semibold whitespace-nowrap">
                                    {formatCurrency(module_summaries.projects.total_budget || 0)}
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                    {/* Sales */}
                    <Card className="shadow-sm">
                        <CardHeader className="px-4 pt-3 pb-2">
                            <CardTitle className="text-sm font-semibold flex items-center gap-2">
                                <TrendingUp className="h-4 w-4 text-emerald-600" />
                                {t('Sales')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="px-4 pb-3 space-y-2">
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Monthly Revenue')}
                                </span>
                                <span className="text-sm font-semibold whitespace-nowrap">
                                    {formatCurrency(module_summaries.sales.monthly_revenue || 0)}
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Quotations')}
                                </span>
                                <span className="text-sm font-semibold text-blue-600">
                                    {module_summaries.sales.quotations_count || 0}
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Orders')}
                                </span>
                                <span className="text-sm font-semibold text-green-600">
                                    {module_summaries.sales.orders_count || 0}
                                </span>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Inventory */}
                    <Card className="shadow-sm">
                        <CardHeader className="px-4 pt-3 pb-2">
                            <CardTitle className="text-sm font-semibold flex items-center gap-2">
                                <Package className="h-4 w-4 text-rose-600" />
                                {t('Inventory')}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="px-4 pb-3 space-y-2">
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Low Stock Items')}
                                </span>
                                <span className="text-sm font-semibold text-yellow-600">
                                    {module_summaries.inventory.low_stock_items}
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Out of Stock')}
                                </span>
                                <span className="text-sm font-semibold text-red-600">
                                    {module_summaries.inventory.out_of_stock}
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-xs text-muted-foreground">
                                    {t('Total Stock Value')}
                                </span>
                                <span className="text-sm font-bold text-green-600 whitespace-nowrap">
                                    {formatCurrency(module_summaries.inventory.total_stock_value || 0)}
                                </span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* Tabbed Data Tables */}
            <div>
                <Card>
                    <CardHeader className="pb-3">
                        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b pb-3">
                            <div className="flex flex-wrap items-center gap-1">
                                <button
                                    onClick={() => setActiveDataTab('employees')}
                                    className={`flex items-center gap-2 px-4 py-2.5 text-sm transition-colors ${getTabButtonClasses('employees')}`}
                                >
                                    <Users className="h-4 w-4" />
                                    {t('Top Employees')}
                                </button>
                                <button
                                    onClick={() => setActiveDataTab('customers')}
                                    className={`flex items-center gap-2 px-4 py-2.5 text-sm transition-colors ${getTabButtonClasses('customers')}`}
                                >
                                    <UserCheck className="h-4 w-4" />
                                    {t('Top Customers')}
                                </button>
                                <button
                                    onClick={() => setActiveDataTab('transactions')}
                                    className={`flex items-center gap-2 px-4 py-2.5 text-sm transition-colors ${getTabButtonClasses('transactions')}`}
                                >
                                    <Clock className="h-4 w-4" />
                                    {t('Transactions')}
                                </button>
                            </div>
                            <div className="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                                {activeDataTab === 'transactions' && (
                                    <>
                                        <Select
                                            value={transactionsPerPage}
                                            onValueChange={(value: string) => {
                                                setTransactionsPerPage(value);
                                                setTransactionsPage(1);
                                            }}
                                        >
                                            <SelectTrigger className="h-9 gap-1.5 text-sm w-full sm:w-auto">
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="10">{t('10 per page')}</SelectItem>
                                                <SelectItem value="20">{t('20 per page')}</SelectItem>
                                                <SelectItem value="50">{t('50 per page')}</SelectItem>
                                                <SelectItem value="100">{t('100 per page')}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                        <ExportButton
                                            data={formattedTransactions || []}
                                            columns={transactionExportColumns}
                                            filename="recent-transactions"
                                            title="All Recent Transactions - Executive Dashboard"
                                        />
                                    </>
                                )}
                                {activeDataTab === 'employees' && (
                                    <ExportButton
                                        data={top_employees || []}
                                        columns={employeeExportColumns}
                                        filename="top-employees"
                                        title="Top 10 Employees - Executive Dashboard"
                                    />
                                )}
                                {activeDataTab === 'customers' && (
                                    <ExportButton
                                        data={top_customers || []}
                                        columns={customerExportColumns}
                                        filename="top-customers"
                                        title="Top 10 Customers - Executive Dashboard"
                                    />
                                )}
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent className="pt-2">
                        {/* Tab: Top 10 Employees */}
                        {activeDataTab === 'employees' && (
                            <>
                                {top_employees && top_employees.length > 0 ? (
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead className="w-10">#</TableHead>
                                                    <TableHead>{t('Employee ID')}</TableHead>
                                                    <TableHead>{t('Name')}</TableHead>
                                                    <TableHead>{t('Department')}</TableHead>
                                                    <TableHead>{t('Designation')}</TableHead>
                                                    <TableHead className="text-center">{t('Tasks')}</TableHead>
                                                    <TableHead className="text-center">{t('Attendance. %')}</TableHead>
                                                    <TableHead className="text-center">{t('Productivity')}</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {top_employees.map((emp, idx) => (
                                                    <TableRow key={idx} className="hover:bg-muted/50">
                                                        <TableCell className="text-sm text-gray-400 font-bold">{idx + 1}</TableCell>
                                                        <TableCell className="text-sm">
                                                            {pageProps?.auth?.user?.permissions?.includes("view-employees") && emp.employee_id ? (
                                                                <Link href={route("hrm.employees.show", emp.id)} className="text-blue-600 hover:text-blue-700 font-medium">
                                                                    {emp.employee_id}
                                                                </Link>
                                                            ) : (
                                                                emp.employee_id || "-"
                                                            )}
                                                        </TableCell>
                                                        <TableCell className="font-medium whitespace-nowrap">{emp.employee_name}</TableCell>
                                                        <TableCell className="text-muted-foreground">{emp.department_name || '-'}</TableCell>
                                                        <TableCell className="text-muted-foreground">{emp.designation_name || '-'}</TableCell>
                                                        <TableCell className="text-center text-green-600 font-medium">{emp.tasks_completed || 0}</TableCell>
                                                        <TableCell className="text-center text-blue-600 font-medium">{emp.attendance_percentage || 0}%</TableCell>
                                                        <TableCell className="text-center font-medium">{Math.round(emp.productivity_score || 0)}</TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                ) : (
                                    <div className="text-center py-8 text-muted-foreground text-sm">{t('No employee data available')}</div>
                                )}
                            </>
                        )}

                        {/* Tab: Top 10 Customers - Card Layout */}
                        {activeDataTab === 'customers' && (
                            <>
                                {top_customers && top_customers.length > 0 ? (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 p-2">
                                        {top_customers.map((cust, idx) => (
                                            <Card key={idx} className="shadow-sm hover:shadow-md transition-all">
                                                <CardContent className="p-3">
                                                    {/* Header: # + Name + Status */}
                                                    <div className="flex items-center justify-between mb-2">
                                                        <span className="text-xs text-gray-400 font-bold">#{idx + 1}</span>
                                                        <span className={`px-1.5 py-0.5 rounded-full text-[10px] font-medium border ${getCustomerStatusStyles(cust.status)}`}>
                                                            {t(cust.status)}
                                                        </span>
                                                    </div>
                                                    <h4 className="text-sm font-semibold mb-2">{cust.customer_name}</h4>
                                                    {/* All Data */}
                                                    <div className="space-y-1.5 text-xs">
                                                        <div className="flex justify-between">
                                                            <span className="text-muted-foreground">{t('Orders')}</span>
                                                            <span className="font-medium">{cust.total_orders || 0}</span>
                                                        </div>
                                                        <div className="flex justify-between">
                                                            <span className="text-muted-foreground">{t('Gross Revenue')}</span>
                                                            <span className="font-medium">{formattedCustomers[idx].gross_revenue}</span>
                                                        </div>
                                                        {(cust.returns_total && cust.returns_total > 0) ? (
                                                            <div className="flex justify-between">
                                                                <span className="text-muted-foreground">{t('Returns')}</span>
                                                                <span className="font-medium text-red-600">-{formattedCustomers[idx].returns_total}</span>
                                                            </div>
                                                        ) : null}
                                                        {(cust.credits_applied && cust.credits_applied > 0) ? (
                                                            <div className="flex justify-between">
                                                                <span className="text-muted-foreground">{t('Credits Applied')}</span>
                                                                <span className="font-medium text-red-600">-{formattedCustomers[idx].credits_applied}</span>
                                                            </div>
                                                        ) : null}
                                                        <div className="flex justify-between border-t pt-1">
                                                            <span className="text-muted-foreground font-semibold">{t('Net Revenue')}</span>
                                                            <span className="font-bold text-green-600">{formattedCustomers[idx].net_revenue}</span>
                                                        </div>
                                                        <div className="flex justify-between">
                                                            <span className="text-muted-foreground">{t('Total Paid')}</span>
                                                            <span className="font-medium">{formattedCustomers[idx].total_paid}</span>
                                                        </div>
                                                        <div className="flex justify-between">
                                                            <span className="text-muted-foreground">{t('Outstanding')}</span>
                                                            <span className="font-medium text-orange-600">{formattedCustomers[idx].outstanding}</span>
                                                        </div>
                                                        <div className="flex justify-between">
                                                            <span className="text-muted-foreground">{t('Avg Order Value')}</span>
                                                            <span className="font-medium">{formattedCustomers[idx].average_order_value}</span>
                                                        </div>
                                                        <div className="flex justify-between">
                                                            <span className="text-muted-foreground">{t('Last Purchase')}</span>
                                                            <span className="font-medium whitespace-nowrap">{formatDate(cust.last_purchase_date, pageProps) || '-'}</span>
                                                        </div>
                                                    </div>
                                                </CardContent>
                                            </Card>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="text-center py-8 text-muted-foreground text-sm">{t('No customer data available')}</div>
                                )}
                            </>
                        )}

                        {/* Tab: Recent Transactions */}
                        {activeDataTab === 'transactions' && (
                            <>
                                {recent_transactions && recent_transactions.length > 0 ? (
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead className="w-10">#</TableHead>
                                                    <TableHead>{t('Date')}</TableHead>
                                                    <TableHead>{t('Type')}</TableHead>
                                                    <TableHead>{t('Module')}</TableHead>
                                                    <TableHead className="text-center">{t('Amount')}</TableHead>
                                                    <TableHead>{t('Status')}</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {paginatedTransactions.length > 0 ? (
                                                    paginatedTransactions.map((txn, idx) => (
                                                        <TableRow key={(transactionsPage - 1) * transactionsPerPageNum + idx}>
                                                            <TableCell className="text-xs text-gray-400 font-bold">{idx + 1}</TableCell>
                                                            <TableCell className="text-muted-foreground whitespace-nowrap">{formatDate(txn.date, pageProps)}</TableCell>
                                                            <TableCell>
                                                                <span className={`px-2 py-0.5 rounded-full text-xs font-medium border ${getTypeBadgeStyles(txn.type)}`}>
                                                                    {txn.type}
                                                                </span>
                                                            </TableCell>
                                                            <TableCell>{txn.module}</TableCell>
                                                            <TableCell className="text-center font-medium whitespace-nowrap">{txn.formatted_amount}</TableCell>
                                                            <TableCell>
                                                                <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize ${getStatusColor(txn.status)}`}>
                                                                    {txn.status}
                                                                </span>
                                                            </TableCell>
                                                        </TableRow>
                                                    ))
                                                ) : (
                                                    <TableRow>
                                                        <TableCell colSpan={5} className="text-center py-8 text-muted-foreground text-sm">
                                                            {t('No transaction data available')}
                                                        </TableCell>
                                                    </TableRow>
                                                )}
                                            </TableBody>
                                        </Table>
                                        {/* Per Page Selector + Pagination Controls */}
                                        <div className="flex items-center justify-between pt-4 pb-1 px-1">
                                            <div className="flex items-center gap-2">
                                                <span className="text-sm text-muted-foreground">
                                                    {t('Showing')} {(transactionsPage - 1) * transactionsPerPageNum + 1} {t('to')} {Math.min(transactionsPage * transactionsPerPageNum, (recent_transactions || []).length)} {t('of')} {(recent_transactions || []).length} {t('results')}
                                                </span>
                                            </div>
                                            {totalTransactionsPages > 1 && (
                                                <div className="flex items-center space-x-2">
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => setTransactionsPage(transactionsPage - 1)}
                                                        disabled={transactionsPage === 1}
                                                        className="h-8 text-sm"
                                                    >
                                                        <ChevronLeft className="h-4 w-4" />
                                                        {t('Previous')}
                                                    </Button>
                                                    <div className="flex items-center space-x-1">
                                                        {Array.from({ length: totalTransactionsPages }, (_, i) => i + 1)
                                                            .filter(page =>
                                                                page === 1 ||
                                                                page === totalTransactionsPages ||
                                                                (page >= transactionsPage - 1 && page <= transactionsPage + 1)
                                                            )
                                                            .map((page, index, array) => (
                                                                <React.Fragment key={page}>
                                                                    {index > 0 && array[index - 1] !== page - 1 && (
                                                                        <span className="px-1 text-xs text-muted-foreground">...</span>
                                                                    )}
                                                                    <Button
                                                                        variant={transactionsPage === page ? "default" : "outline"}
                                                                        size="sm"
                                                                        onClick={() => setTransactionsPage(page)}
                                                                        className="h-8 w-8 p-0 text-xs"
                                                                    >
                                                                        {page}
                                                                    </Button>
                                                                </React.Fragment>
                                                            ))
                                                        }
                                                    </div>
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => setTransactionsPage(transactionsPage + 1)}
                                                        disabled={transactionsPage === totalTransactionsPages}
                                                        className="h-8 text-xs"
                                                    >
                                                        {t('Next')}
                                                        <ChevronRight className="h-4 w-4" />
                                                    </Button>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                ) : (
                                    <div className="text-center py-8 text-muted-foreground text-sm">{t('No transaction data available')}</div>
                                )}
                            </>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}