declare const route: (name: string, params?: any) => string;
import React, { useState } from "react";
import { Head, usePage, Link, router } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import AuthenticatedLayout from "@/layouts/authenticated-layout";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { DatePicker } from "@/components/ui/date-picker";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { LineChart } from "@/components/charts/LineChart";
import { BarChart } from "@/components/charts/BarChart";
import { PieChart } from "@/components/charts/PieChart";
import { AreaChart } from "@/components/charts/AreaChart";
import { formatCurrency, formatDate } from "@/utils/helpers";
import ExportButton from "../../Components/ExportButton";
import {
    DollarSign, TrendingUp, TrendingDown, Activity, Wallet, Filter, CreditCard,
    ArrowUpRight, ArrowDownRight, FileText, ChevronDown, ChevronUp, ChevronLeft, ChevronRight,
} from "lucide-react";

interface RevenueSource {
    reference_type: string;
    revenue_amount: number;
}
interface TrendData {
    month: string;
    total: number;
}
interface Transaction {
    id: number;
    journal_number: string;
    date: string;
    entry_type?: string;
    reference_type: string;
    description: string;
    amount?: number;
    total_debit?: number;
    total_credit?: number;
    status: string;
    expense_account?: string;
    account_name?: string;
    reference_id?: number;
    items?: Array<{
        account_name: string;
        account_code: string;
        debit_amount: number;
        credit_amount: number;
    }>;
}
interface PnLSummary {
    gross_revenue: number;
    total_expenses: number;
    net_profit: number;
    profit_margin: number;
}
interface ProfitTrend {
    month: string;
    revenue: number;
    expense: number;
}
interface CashFlowForecast {
    journal_date: string;
    net_cash_flow: number;
}
interface AgingItem {
    customer_id?: number;
    customer?: string;
    customer_email?: string;
    vendor_id?: number;
    vendor?: string;
    invoice_number?: string;
    amount?: number;
    total_invoiced?: number;
    total_returns?: number;
    net_invoiced?: number;
    total_paid?: number;
    balance?: number;
    due_date: string;
    status: string;
    days_overdue?: number;
    days_until_due?: number;
}
interface FinancialAnalyticsProps extends Record<string, any> {
    revenue_analysis: {
        kpi: {
            total_revenue: number;
            revenue_growth: number;
            avg_daily_revenue: number;
        };
        by_source: RevenueSource[];
        trend: TrendData[];
        transactions: {
            data: Transaction[];
            current_page: number;
            last_page: number;
            per_page: number;
            total: number;
        };
    };
    expense_analysis: {
        kpi: {
            total_expenses: number;
            expense_growth: number;
            avg_daily_expense: number;
        };
        by_category: Array<{ expense_category: string; total_expense: number }>;
        trend: TrendData[];
        transactions: { data: Transaction[] };
    };
    profitability: {
        summary: PnLSummary;
        trend: ProfitTrend[];
        by_transaction_type: Array<{
            transaction_type: string;
            revenue: number;
            cost: number;
            profit: number;
            margin: number;
        }>;
        by_account: Array<{
            account_name: string;
            account_code: string;
            account_type: string;
            amount: number;
        }>;
        by_month: Array<{
            month: string;
            revenue: number;
            expense: number;
            profit: number;
        }>;
        waterfall: Array<{ label: string; value: number; type: string }>;
    };
    cash_flow: {
        kpi: { cash_balance: number; accounts_receivable: number; accounts_payable: number };
        forecast: CashFlowForecast[];
        ar_aging: AgingItem[];
        ap_aging: AgingItem[];
    };
    journal_entries: { data: Transaction[] };
}

// Compact KPI card — small footprint so the stat row doesn't eat the screen.
function Kpi({ label, value, icon: Icon, tone = "default" }: { label: string; value: React.ReactNode; icon?: any; tone?: "default" | "green" | "blue" | "yellow" | "red" | "orange" | "purple" | "cyan" }) {
    const toneClass: Record<string, string> = {
        default: "text-foreground",
        green: "text-green-600",
        blue: "text-blue-600",
        yellow: "text-yellow-600",
        red: "text-red-600",
        orange: "text-orange-600",
        purple: "text-purple-600",
        cyan: "text-cyan-600",
    };
    return (
        <Card className="shadow-none">
            <CardContent className="py-3 px-4 flex items-center justify-between gap-2">
                <div>
                    <div className="text-[11px] font-medium text-muted-foreground leading-tight mb-1">{label}</div>
                    <div className={`text-xl font-bold leading-tight ${toneClass[tone]}`}>{value}</div>
                </div>
                {Icon && <Icon className={`h-4 w-4 shrink-0 ${toneClass[tone]}`} />}
            </CardContent>
        </Card>
    );
}

export default function FinancialAnalytics() {
    const { t } = useTranslation();
    const page = usePage<FinancialAnalyticsProps>();
    const props = page.props;
    const { revenue_analysis, expense_analysis, profitability, cash_flow, journal_entries } = props;
    const pageProps = page.props as any;

    const [showTxFilters, setShowTxFilters] = useState(false);
    const [showExpenseFilters, setShowExpenseFilters] = useState(false);
    const [showJournalFilters, setShowJournalFilters] = useState(false);
    const [expandedJournal, setExpandedJournal] = useState<string | number | null>(null);
    const [txnPage, setTxnPage] = useState(1);
    const [expPage, setExpPage] = useState(1);
    const [txnPerPage, setTxnPerPage] = useState("10");
    const [expPerPage, setExpPerPage] = useState("10");

    const [allTxns, setAllTxns] = useState(revenue_analysis.transactions?.data || []);
    const [allExpTxns, setAllExpTxns] = useState(expense_analysis.transactions?.data || []);

    const [txFilters, setTxFilters] = useState({
        date_from: "",
        date_to: "",
        reference_type: "all",
        account: "",
        status: "all",
        sort: "date",
        direction: "desc" as "asc" | "desc",
    });
    const [expFilters, setExpFilters] = useState({
        date_from: "",
        date_to: "",
        reference_type: "all",
        expense_category: "all",
        status: "all",
        sort: "date",
        direction: "desc" as "asc" | "desc",
    });

    // Journal filters state
    const [journalFilters, setJournalFilters] = useState({ date_from: "", date_to: "", status: "all" });
    const [journalPage, setJournalPage] = useState(1);
    const [journalPerPage, setJournalPerPage] = useState("10");
    const [allJournalEntries, setAllJournalEntries] = useState(journal_entries?.data || []);
    const handleJournalFiltersChange = (key: string, value: string) =>
        setJournalFilters((prev) => ({ ...prev, [key]: value }));

    const fetchJournalEntries = async (filters: any) => {
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([key, value]) => {
            if (value !== "" && value !== null && value !== undefined) params.append(key, String(value));
        });
        try {
            const res = await fetch(route("smart-analytics.financial.journal-txns") + "?" + params.toString(), {
                headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await res.json();
            setAllJournalEntries(data.data || []);
        } catch (err) {
            console.error("Failed to fetch journal entries", err);
        }
    };

    const journalPerPageNum = parseInt(journalPerPage, 10);
    const totalJournalPages = Math.ceil(allJournalEntries.length / journalPerPageNum);
    const paginatedJournalEntries = allJournalEntries.slice(
        (journalPage - 1) * journalPerPageNum,
        journalPage * journalPerPageNum
    );
    const journalFrom = allJournalEntries.length > 0 ? (journalPage - 1) * journalPerPageNum + 1 : 0;
    const journalTo = Math.min(journalPage * journalPerPageNum, allJournalEntries.length);

    const handleJournalSearch = () => {
        setJournalPage(1);
        fetchJournalEntries(journalFilters);
    };
    const handleJournalReset = () => {
        const resetFilters = { date_from: "", date_to: "", status: "all" };
        setJournalFilters(resetFilters);
        setJournalPage(1);
        fetchJournalEntries(resetFilters);
    };
    const [journalSortField, setJournalSortField] = useState("date");
    const [journalSortDir, setJournalSortDir] = useState<"asc" | "desc">("desc");

    const handleJournalSort = (field: string) => {
        const newDir: "asc" | "desc" = journalSortField === field && journalSortDir === "desc" ? "asc" : "desc";
        setJournalSortField(field);
        setJournalSortDir(newDir);
        setAllJournalEntries((prev) => {
            const sorted = [...prev];
            sorted.sort((a: any, b: any) => {
                const av = a[field] ?? "";
                const bv = b[field] ?? "";
                if (typeof av === "number" && typeof bv === "number") return newDir === "asc" ? av - bv : bv - av;
                return newDir === "asc" ? String(av).localeCompare(String(bv)) : String(bv).localeCompare(String(av));
            });
            return sorted;
        });
    };

    const getJournalSortIcon = (field: string) => {
        if (journalSortField !== field) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return journalSortDir === "asc" ? <ChevronUp className="h-4 w-4 inline ml-1" /> : <ChevronDown className="h-4 w-4 inline ml-1" />;
    };
    const getJournalPageNumbers = () => {
        const p: (number | string)[] = [];
        if (totalJournalPages <= 7) {
            for (let i = 1; i <= totalJournalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (journalPage > 3) p.push("...");
            const s = Math.max(2, journalPage - 1);
            const e = Math.min(totalJournalPages - 1, journalPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (journalPage < totalJournalPages - 2) p.push("...");
            p.push(totalJournalPages);
        }
        return p;
    };

    // Profitability toggle
    const [profitView, setProfitView] = useState<"type" | "account" | "month">("type");
    const [profitSortField, setProfitSortField] = useState("profit");
    const [profitSortDir, setProfitSortDir] = useState<"asc" | "desc">("desc");

    const txnPerPageNum = parseInt(txnPerPage, 10);
    const totalTxnPages = Math.ceil(allTxns.length / txnPerPageNum);
    const paginatedTxns = allTxns.slice((txnPage - 1) * txnPerPageNum, txnPage * txnPerPageNum);
    const txnFrom = allTxns.length > 0 ? (txnPage - 1) * txnPerPageNum + 1 : 0;
    const txnTo = Math.min(txnPage * txnPerPageNum, allTxns.length);

    const expPerPageNum = parseInt(expPerPage, 10);
    const totalExpPages = Math.ceil(allExpTxns.length / expPerPageNum);
    const paginatedExpTxns = allExpTxns.slice((expPage - 1) * expPerPageNum, expPage * expPerPageNum);
    const expFrom = allExpTxns.length > 0 ? (expPage - 1) * expPerPageNum + 1 : 0;
    const expTo = Math.min(expPage * expPerPageNum, allExpTxns.length);

    const fetchRevenueTxns = async (filters: any) => {
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([key, value]) => {
            if (value !== "" && value !== null && value !== undefined) params.append(key, String(value));
        });
        try {
            const res = await fetch(route("smart-analytics.financial.revenue-txns") + "?" + params.toString(), {
                headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await res.json();
            setAllTxns(data.data || []);
        } catch (err) {
            console.error("Failed to fetch revenue transactions", err);
        }
    };
    const fetchExpenseTxns = async (filters: any) => {
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([key, value]) => {
            if (value !== "" && value !== null && value !== undefined) params.append(key, String(value));
        });
        try {
            const res = await fetch(route("smart-analytics.financial.expense-txns") + "?" + params.toString(), {
                headers: { "X-Requested-With": "XMLHttpRequest", Accept: "application/json" },
                credentials: "same-origin",
            });
            const data = await res.json();
            setAllExpTxns(data.data || []);
        } catch (err) {
            console.error("Failed to fetch expense transactions", err);
        }
    };

    const handleTxSort = (field: string) => {
        const d: "asc" | "desc" = txFilters.sort === field && txFilters.direction === "desc" ? "asc" : "desc";
        const ns = { ...txFilters, sort: field, direction: d };
        setTxFilters(ns);
        setTxnPage(1);
        fetchRevenueTxns(ns);
    };
    const handleTxSearch = () => {
        setTxnPage(1);
        fetchRevenueTxns(txFilters);
    };
    const getSortIcon = (f: string) => {
        if (txFilters.sort !== f) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return txFilters.direction === "asc" ? <ChevronUp className="h-4 w-4 inline ml-1" /> : <ChevronDown className="h-4 w-4 inline ml-1" />;
    };
    const getPageNumbers = () => {
        const p: (number | string)[] = [];
        if (totalTxnPages <= 7) {
            for (let i = 1; i <= totalTxnPages; i++) p.push(i);
        } else {
            p.push(1);
            if (txnPage > 3) p.push("...");
            const s = Math.max(2, txnPage - 1);
            const e = Math.min(totalTxnPages - 1, txnPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (txnPage < totalTxnPages - 2) p.push("...");
            p.push(totalTxnPages);
        }
        return p;
    };
    const handleTxPaginationClient = (p: number) => setTxnPage(p);
    const handleTxFiltersChange = (k: string, v: string) => setTxFilters((prev) => ({ ...prev, [k]: v }));

    const handleExpSort = (field: string) => {
        const d: "asc" | "desc" = expFilters.sort === field && expFilters.direction === "desc" ? "asc" : "desc";
        const ns = { ...expFilters, sort: field, direction: d };
        setExpFilters(ns);
        setExpPage(1);
        fetchExpenseTxns(ns);
    };
    const getExpSortIcon = (f: string) => {
        if (expFilters.sort !== f) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return expFilters.direction === "asc" ? <ChevronUp className="h-4 w-4 inline ml-1" /> : <ChevronDown className="h-4 w-4 inline ml-1" />;
    };
    const handleExpSearch = () => {
        setExpPage(1);
        fetchExpenseTxns(expFilters);
    };
    const handleExpFiltersChange = (k: string, v: string) => setExpFilters((prev) => ({ ...prev, [k]: v }));
    const getExpPageNumbers = () => {
        const p: (number | string)[] = [];
        if (totalExpPages <= 7) {
            for (let i = 1; i <= totalExpPages; i++) p.push(i);
        } else {
            p.push(1);
            if (expPage > 3) p.push("...");
            const s = Math.max(2, expPage - 1);
            const e = Math.min(totalExpPages - 1, expPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (expPage < totalExpPages - 2) p.push("...");
            p.push(totalExpPages);
        }
        return p;
    };
    const handleExpPaginationClient = (p: number) => setExpPage(p);

    const handleProfitSort = (field: string) => {
        const newDir: "asc" | "desc" = profitSortField === field && profitSortDir === "desc" ? "asc" : "desc";
        setProfitSortField(field);
        setProfitSortDir(newDir);
    };
    const getProfitSortIcon = (field: string) => {
        if (profitSortField !== field) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return profitSortDir === "asc" ? <ChevronUp className="h-4 w-4 inline ml-1" /> : <ChevronDown className="h-4 w-4 inline ml-1" />;
    };

    const sortableLabels: Record<string, string> = {
        journal_number: t("Journal #"),
        date: t("Date"),
        reference_type: t("Transaction Type"),
        description: t("Description"),
        amount: t("Amount"),
        account_name: t("Account"),
        status: t("Status"),
    };
    const expSortableLabels: Record<string, string> = {
        journal_number: t("Journal #"),
        date: t("Date"),
        reference_type: t("Transaction Type"),
        description: t("Description"),
        amount: t("Amount"),
        expense_account: t("Expense Account"),
        status: t("Status"),
    };
    const sortableHeaders = ["journal_number", "date", "reference_type", "description", "amount", "account_name", "status"];
    const expSortableHeaders = ["journal_number", "date", "reference_type", "description", "amount", "expense_account", "status"];

    const sourceLabels: Record<string, string> = {
        sales_invoice: t("Sales Invoices"),
        pos_sale: t("POS Sales"),
        pos_return: t("POS Return"),
        customer_payment: t("Customer Payment"),
        vendor_payment: t("Vendor Payment"),
        revenue: t("Other Revenue"),
        service_invoice: t("Service Invoices"),
        project_payment: t("Project Payment"),
        sales_invoice_cogs: t("Sales Invoice COGS"),
        expense: t("Expenses"),
        pos_sale_cogs: t("POS Sale COGS"),
        pos_return_cogs: t("POS Return COGS"),
        credit_note_cogs: t("Credit Note COGS"),
        purchase_invoice: t("Purchase Invoices"),
        purchase_invoice_cogs: t("Purchase Invoice COGS"),
        general_expense: t("General Expenses"),
        operational_expense: t("Operational Expenses"),
        retainer_to_invoice: t("Retainer to Invoice"),
        credit_note: t("Credit Note"),
    };
    const sourceColors: Record<string, string> = {
        sales_invoice: "#3b82f6",
        pos_sale: "#10b981",
        revenue: "#f59e0b",
        service_invoice: "#8b5cf6",
    };

    const getStatusBadgeStyle = (status: string) => {
        switch (status) {
            case "posted":
            case "paid":
            case "completed":
                return "bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800";
            case "pending":
                return "bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800";
            case "cancelled":
                return "bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800";
            case "draft":
            default:
                return "bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700";
        }
    };

    // Profit table data based on view
    let profitTableData: any[] = [];
    let profitColumns: { key: string; header: string; render?: (v: any, r: any) => string }[] = [];

    if (profitView === "type") {
        profitTableData = [...(profitability.by_transaction_type || [])].sort((a, b) => {
            const av = a[profitSortField as keyof typeof a] ?? 0;
            const bv = b[profitSortField as keyof typeof b] ?? 0;
            return profitSortDir === "desc" ? (bv > av ? 1 : -1) : av > bv ? 1 : -1;
        });
        profitColumns = [
            { key: "transaction_type", header: t("Transaction Type"), render: (v: string) => sourceLabels[v] || v },
            { key: "revenue", header: t("Revenue"), render: (v: number) => formatCurrency(v || 0, pageProps) },
            { key: "cost", header: t("Cost"), render: (v: number) => formatCurrency(v || 0, pageProps) },
            { key: "profit", header: t("Profit"), render: (v: number) => formatCurrency(v || 0, pageProps) },
            { key: "margin", header: t("Margin %"), render: (v: number) => `${v}%` },
        ];
    } else if (profitView === "account") {
        profitTableData = [...(profitability.by_account || [])].sort((a, b) => {
            const av = a[profitSortField as keyof typeof a] ?? 0;
            const bv = b[profitSortField as keyof typeof b] ?? 0;
            return profitSortDir === "desc" ? (bv > av ? 1 : -1) : av > bv ? 1 : -1;
        });
        profitColumns = [
            { key: "account_name", header: t("Account"), render: (v: string) => v },
            { key: "account_code", header: t("Code") },
            { key: "account_type", header: t("Type"), render: (v: string) => (v === "revenue" ? t("Revenue") : t("Expense")) },
            { key: "amount", header: t("Amount"), render: (v: number) => formatCurrency(v || 0, pageProps) },
        ];
    } else if (profitView === "month") {
        profitTableData = [...(profitability.by_month || [])].sort((a, b) => {
            if (profitSortField === "month") {
                return profitSortDir === "desc" ? b.month.localeCompare(a.month) : a.month.localeCompare(b.month);
            }
            const av = a[profitSortField as keyof typeof a] ?? 0;
            const bv = b[profitSortField as keyof typeof b] ?? 0;
            return profitSortDir === "desc" ? (bv > av ? 1 : -1) : av > bv ? 1 : -1;
        });
        profitColumns = [
            { key: "month", header: t("Month") },
            { key: "revenue", header: t("Revenue"), render: (v: number) => formatCurrency(v || 0, pageProps) },
            { key: "expense", header: t("Expense"), render: (v: number) => formatCurrency(v || 0, pageProps) },
            { key: "profit", header: t("Profit"), render: (v: number) => formatCurrency(v || 0, pageProps) },
        ];
    }

    // Tab definitions used to render the nav strip — keeping this as data
    // means adding/removing a section never requires touching markup twice.
    const tabs = [
        { value: "revenue", label: t("Revenue"), icon: DollarSign },
        { value: "expenses", label: t("Expenses"), icon: CreditCard },
        { value: "profitability", label: t("Profitability"), icon: Activity },
        { value: "cashflow", label: t("Cash Flow"), icon: Wallet },
        { value: "journal", label: t("Journal Entries"), icon: FileText },
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Smart Dashboard'), url: route('smart-analytics.dashboard') },
                {label: t('Financial Analytics')}
            ]}
            pageTitle={t("Financial Analytics")}
        >
            <Head title={t("Financial Analytics")} />

            <Tabs defaultValue="revenue" className="w-full">
                {/* Sticky tab strip — one section visible at a time, so the page
                   that used to be five screens tall is now one screen per tab. */}
                <TabsList className="sticky top-0 z-10 mb-6 h-auto w-full flex-wrap justify-start gap-1 overflow-x-auto bg-muted/60 p-1.5 backdrop-blur supports-[backdrop-filter]:bg-muted/50">
                    {tabs.map(({ value, label, icon: Icon }) => (
                        <TabsTrigger key={value} value={value} className="gap-1.5 whitespace-nowrap text-xs sm:text-sm">
                            <Icon className="h-3.5 w-3.5" />
                            {label}
                        </TabsTrigger>
                    ))}
                </TabsList>

                {/* ==================== TAB 1: REVENUE ANALYSIS ==================== */}
                <TabsContent value="revenue" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                        <Kpi label={t("Total Revenue")} value={formatCurrency(revenue_analysis.kpi.total_revenue || 0)} icon={DollarSign} tone="green" />
                        <Kpi
                            label={t("Revenue Growth")}
                            value={
                                <span className="inline-flex items-center gap-1">
                                    {revenue_analysis.kpi.revenue_growth >= 0 ? (
                                        <ArrowUpRight className="h-4 w-4 text-green-600" />
                                    ) : (
                                        <ArrowDownRight className="h-4 w-4 text-red-600" />
                                    )}
                                    {revenue_analysis.kpi.revenue_growth}%
                                </span>
                            }
                            icon={TrendingUp}
                            tone="blue"
                        />
                        <Kpi label={t("Avg Daily Revenue")} value={formatCurrency(revenue_analysis.kpi.avg_daily_revenue || 0)} icon={Activity} tone="purple" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t("Revenue by Source")}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {revenue_analysis.by_source && revenue_analysis.by_source.length > 0 ? (
                                    <PieChart
                                        data={revenue_analysis.by_source.map((s) => ({
                                            ...s,
                                            name: sourceLabels[s.reference_type] || s.reference_type,
                                            color: sourceColors[s.reference_type] || "#6b7280",
                                        }))}
                                        dataKey="revenue_amount"
                                        nameKey="name"
                                        donut
                                        showLegend
                                        showTooltip
                                        height={250}
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full">
                                        <PieChart
                                            data={[
                                                { name: t("No Data"), value: 100, color: "#e5e7eb" }
                                            ]}
                                            dataKey="value"
                                            nameKey="name"
                                            donut
                                            showLegend={false}
                                            showTooltip={false}
                                            separatorNone={true}
                                            height={250}
                                        />
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t("Revenue Trend (12 months)")}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {revenue_analysis.trend && revenue_analysis.trend.length > 0 ? (
                                    <LineChart
                                        data={revenue_analysis.trend.map((t: any) => ({
                                            month: t.month,
                                            'Total Revenue': t.total || 0
                                        }))}
                                        dataKey="Total Revenue"
                                        xAxisKey="month"
                                        color="#10b981"
                                        showDots
                                        showTooltip
                                        height={250}
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t("No trend data available")}</div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap">
                            <CardTitle className="text-md">{t("Revenue Transactions")}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Button variant="outline" size="sm" className="h-9 gap-1.5 text-sm" onClick={() => setShowTxFilters(!showTxFilters)}>
                                    <Filter className="h-4 w-4" />
                                    {t("Filters")}
                                    {showTxFilters ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}
                                </Button>
                                <Select value={txnPerPage} onValueChange={(v: string) => { setTxnPerPage(v); setTxnPage(1); }}>
                                    <SelectTrigger className="h-9 gap-1.5 text-sm w-32">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="10">{t("10 per page")}</SelectItem>
                                        <SelectItem value="20">{t("20 per page")}</SelectItem>
                                        <SelectItem value="50">{t("50 per page")}</SelectItem>
                                        <SelectItem value="100">{t("100 per page")}</SelectItem>
                                    </SelectContent>
                                </Select>
                                <ExportButton
                                    data={allTxns}
                                    columns={[
                                        { key: "journal_number", header: t("Journal #") },
                                        { key: "date", header: t("Date") },
                                        { key: "reference_type", header: t("Transaction Type"), render: (v: string) => sourceLabels[v] || v },
                                        { key: "description", header: t("Description") },
                                        { key: "amount", header: t("Amount"), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: "account_name", header: t("Account") },
                                        { key: "status", header: t("Status") },
                                    ]}
                                    filename="revenue-transactions"
                                    title={t("Revenue Transactions")}
                                />
                            </div>
                        </CardHeader>
                        {showTxFilters && (
                            <CardContent className="border-b p-4">
                                <div className="grid grid-cols-1 md:grid-cols-5 gap-3">
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Date From")}</label>
                                        <DatePicker value={txFilters.date_from} onChange={(v: string) => handleTxFiltersChange("date_from", v)} placeholder={t("Select date")} />
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Date To")}</label>
                                        <DatePicker value={txFilters.date_to} onChange={(v: string) => handleTxFiltersChange("date_to", v)} placeholder={t("Select date")} />
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Transaction Type")}</label>
                                        <Select value={txFilters.reference_type} onValueChange={(v: string) => handleTxFiltersChange("reference_type", v)}>
                                            <SelectTrigger className="h-10 w-full">
                                                <SelectValue placeholder={t("All")} />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="all">{t("All")}</SelectItem>
                                                <SelectItem value="sales_invoice">{t("Sales Invoices")}</SelectItem>
                                                <SelectItem value="pos_sale">{t("POS Sales")}</SelectItem>
                                                <SelectItem value="service_invoice">{t("Service Invoices")}</SelectItem>
                                                <SelectItem value="revenue">{t("Other Revenue")}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Status")}</label>
                                        <Select value={txFilters.status} onValueChange={(v: string) => handleTxFiltersChange("status", v)}>
                                            <SelectTrigger className="h-10 w-full">
                                                <SelectValue placeholder={t("All")} />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="all">{t("All")}</SelectItem>
                                                <SelectItem value="posted">{t("Posted")}</SelectItem>
                                                <SelectItem value="draft">{t("Draft")}</SelectItem>
                                                <SelectItem value="pending">{t("Pending")}</SelectItem>
                                                <SelectItem value="cancelled">{t("Cancelled")}</SelectItem>
                                                <SelectItem value="completed">{t("Completed")}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div className="flex items-end gap-2">
                                        <Button className="h-10" size="sm" onClick={handleTxSearch}>{t("Apply")}</Button>
                                        <Button
                                            className="h-10"
                                            variant="outline"
                                            size="sm"
                                            onClick={() => {
                                                const reset = { date_from: "", date_to: "", reference_type: "all", account: "", status: "all", sort: "date", direction: "desc" as "asc" | "desc" };
                                                setTxFilters(reset);
                                                setTxnPage(1);
                                                fetchRevenueTxns(reset);
                                            }}
                                        >
                                            {t("Reset")}
                                        </Button>
                                    </div>
                                </div>
                            </CardContent>
                        )}
                        <CardContent className="p-0">
                            {allTxns.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    {sortableHeaders.map((f) => (
                                                        <TableHead key={f} className={`cursor-pointer select-none hover:bg-muted/50 ${f === "amount" ? "text-right" : ""}`} onClick={() => handleTxSort(f)}>
                                                            {sortableLabels[f]}
                                                            {getSortIcon(f)}
                                                        </TableHead>
                                                    ))}
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {paginatedTxns.map((txn: Transaction) => (
                                                    <TableRow key={txn.id} className="hover:bg-muted/50">
                                                        <TableCell className="font-medium">{txn.journal_number}</TableCell>
                                                        <TableCell className="whitespace-nowrap">{formatDate(txn.date, pageProps)}</TableCell>
                                                        <TableCell>
                                                            <Badge variant="secondary" className="text-xs">{sourceLabels[txn.reference_type] || txn.reference_type}</Badge>
                                                        </TableCell>
                                                        <TableCell className="max-w-[200px]">{txn.description || "-"}</TableCell>
                                                        <TableCell className="text-right font-medium">{formatCurrency(txn.amount || 0)}</TableCell>
                                                        <TableCell className="text-xs max-w-[150px]">{(txn as any).account_name || "-"}</TableCell>
                                                        <TableCell>
                                                            <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize ${getStatusBadgeStyle(txn.status)}`}>{t(txn.status)}</span>
                                                        </TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t("Showing")} {txnFrom} {t("to")} {txnTo} {t("of")} {allTxns.length} {t("results")}
                                        </span>
                                        {totalTxnPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button variant="outline" size="sm" onClick={() => handleTxPaginationClient(txnPage - 1)} disabled={txnPage === 1} className="h-8 text-sm">
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t("Previous")}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getPageNumbers().map((p, i) =>
                                                        typeof p === "number" ? (
                                                            <Button key={i} variant={txnPage === p ? "default" : "outline"} size="sm" onClick={() => handleTxPaginationClient(p)} className="h-8 w-8 p-0 text-xs">
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button variant="outline" size="sm" onClick={() => handleTxPaginationClient(txnPage + 1)} disabled={txnPage === totalTxnPages} className="h-8 text-xs">
                                                    {t("Next")}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t("No revenue transactions")}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 2: EXPENSE ANALYSIS ==================== */}
                <TabsContent value="expenses" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                        <Kpi label={t("Total Expenses")} value={formatCurrency(expense_analysis.kpi.total_expenses || 0)} icon={CreditCard} tone="red" />
                        <Kpi label={t("Expense Growth")} value={`${expense_analysis.kpi.expense_growth}%`} icon={TrendingDown} tone="orange" />
                        <Kpi label={t("Avg Daily Expense")} value={formatCurrency(expense_analysis.kpi.avg_daily_expense || 0)} icon={Activity} tone="red" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t("Expense by Category")}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {expense_analysis.by_category && expense_analysis.by_category.length > 0 ? (
                                    <BarChart
                                        data={expense_analysis.by_category.map((e: any) => ({
                                            expense_category: e.expense_category,
                                            'Total Expense': parseFloat(e.total_expense) || 0
                                        }))}
                                        dataKey="Total Expense"
                                        xAxisKey="expense_category"
                                        color="#ef4444"
                                        height={250}
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full">
                                        <PieChart
                                            data={[
                                                { name: t("No Data"), value: 100, color: "#e5e7eb" }
                                            ]}
                                            dataKey="value"
                                            nameKey="name"
                                            donut
                                            showLegend={false}
                                            showTooltip={false}
                                            height={250}
                                        />
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t("Expense Trend (12 months)")}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {expense_analysis.trend && expense_analysis.trend.length > 0 ? (
                                    <LineChart
                                        data={expense_analysis.trend.map((t: any) => ({
                                            month: t.month,
                                            'Total Expense': t.total || 0
                                        }))}
                                        dataKey="Total Expense"
                                        xAxisKey="month"
                                        color="#ef4444"
                                        showDots
                                        showTooltip
                                        height={250}
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t("No trend data available")}</div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap">
                            <CardTitle className="text-md">{t("Expense Transactions")}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Button variant="outline" size="sm" className="h-9 gap-1.5 text-sm" onClick={() => setShowExpenseFilters(!showExpenseFilters)}>
                                    <Filter className="h-4 w-4" />
                                    {t("Filters")}
                                    {showExpenseFilters ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}
                                </Button>
                                <Select value={expPerPage} onValueChange={(v: string) => { setExpPerPage(v); setExpPage(1); }}>
                                    <SelectTrigger className="h-9 gap-1.5 text-sm w-32">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="10">{t("10 per page")}</SelectItem>
                                        <SelectItem value="20">{t("20 per page")}</SelectItem>
                                        <SelectItem value="50">{t("50 per page")}</SelectItem>
                                        <SelectItem value="100">{t("100 per page")}</SelectItem>
                                    </SelectContent>
                                </Select>
                                <ExportButton
                                    data={allExpTxns}
                                    columns={[
                                        { key: "journal_number", header: t("Journal #") },
                                        { key: "date", header: t("Date") },
                                        { key: "reference_type", header: t("Transaction Type") },
                                        { key: "description", header: t("Description") },
                                        { key: "amount", header: t("Amount"), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: "expense_account", header: t("Expense Account") },
                                        { key: "status", header: t("Status") },
                                    ]}
                                    filename="expense-transactions"
                                    title={t("Expense Transactions")}
                                />
                            </div>
                        </CardHeader>
                        {showExpenseFilters && (
                            <CardContent className="border-b p-4">
                                <div className="grid grid-cols-1 md:grid-cols-5 gap-3">
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Date From")}</label>
                                        <DatePicker value={expFilters.date_from} onChange={(v: string) => handleExpFiltersChange("date_from", v)} placeholder={t("Select date")} />
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Date To")}</label>
                                        <DatePicker value={expFilters.date_to} onChange={(v: string) => handleExpFiltersChange("date_to", v)} placeholder={t("Select date")} />
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Transaction Type")}</label>
                                        <Select value={expFilters.reference_type} onValueChange={(v: string) => handleExpFiltersChange("reference_type", v)}>
                                            <SelectTrigger className="h-10 w-full">
                                                <SelectValue placeholder={t("All")} />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="all">{t("All")}</SelectItem>
                                                <SelectItem value="purchase_invoice">{t("Purchase Invoices")}</SelectItem>
                                                <SelectItem value="expense">{t("Expenses")}</SelectItem>
                                                <SelectItem value="sales_invoice_cogs">{t("Sales Invoice COGS")}</SelectItem>
                                                <SelectItem value="pos_sale_cogs">{t("POS Sale COGS")}</SelectItem>
                                                <SelectItem value="purchase_invoice_cogs">{t("Purchase Invoice COGS")}</SelectItem>
                                                <SelectItem value="general_expense">{t("General Expenses")}</SelectItem>
                                                <SelectItem value="operational_expense">{t("Operational Expenses")}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Expense Category")}</label>
                                        <Select value={expFilters.expense_category} onValueChange={(v: string) => handleExpFiltersChange("expense_category", v)}>
                                            <SelectTrigger className="h-10 w-full">
                                                <SelectValue placeholder={t("All")} />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="all">{t("All")}</SelectItem>
                                                {(expense_analysis.by_category || []).map((cat: any) => (
                                                    <SelectItem key={cat.expense_category} value={cat.expense_category}>{cat.expense_category}</SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div className="flex items-end gap-2">
                                        <Button className="h-10" size="sm" onClick={handleExpSearch}>{t("Apply")}</Button>
                                        <Button
                                            className="h-10"
                                            variant="outline"
                                            size="sm"
                                            onClick={() => {
                                                const reset = { date_from: "", date_to: "", reference_type: "all", expense_category: "all", status: "all", sort: "date", direction: "desc" as "asc" | "desc" };
                                                setExpFilters(reset);
                                                setExpPage(1);
                                                fetchExpenseTxns(reset);
                                            }}
                                        >
                                            {t("Reset")}
                                        </Button>
                                    </div>
                                </div>
                            </CardContent>
                        )}
                        <CardContent className="p-0">
                            {allExpTxns.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    {expSortableHeaders.map((f) => (
                                                        <TableHead key={f} className={`cursor-pointer select-none hover:bg-muted/50 ${f === "amount" ? "text-right" : ""}`} onClick={() => handleExpSort(f)}>
                                                            {expSortableLabels[f]}
                                                            {getExpSortIcon(f)}
                                                        </TableHead>
                                                    ))}
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {paginatedExpTxns.map((txn: Transaction, idx: number) => (
                                                    <TableRow key={idx} className="hover:bg-muted/50">
                                                        <TableCell className="font-medium">{txn.journal_number}</TableCell>
                                                        <TableCell className="whitespace-nowrap">{formatDate(txn.date, pageProps)}</TableCell>
                                                        <TableCell>
                                                            <Badge variant="secondary" className="text-xs">{sourceLabels[txn.reference_type] || txn.reference_type}</Badge>
                                                        </TableCell>
                                                        <TableCell className="max-w-[200px]">{txn.description || "-"}</TableCell>
                                                        <TableCell className="text-right font-medium">{formatCurrency(txn.amount || 0)}</TableCell>
                                                        <TableCell className="text-xs max-w-[150px]">{(txn as any).expense_account || "-"}</TableCell>
                                                        <TableCell>
                                                            <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize ${getStatusBadgeStyle(txn.status)}`}>{t(txn.status)}</span>
                                                        </TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t("Showing")} {expFrom} {t("to")} {expTo} {t("of")} {allExpTxns.length} {t("results")}
                                        </span>
                                        {totalExpPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button variant="outline" size="sm" onClick={() => handleExpPaginationClient(expPage - 1)} disabled={expPage === 1} className="h-8 text-sm">
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t("Previous")}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getExpPageNumbers().map((p, i) =>
                                                        typeof p === "number" ? (
                                                            <Button key={i} variant={expPage === p ? "default" : "outline"} size="sm" onClick={() => handleExpPaginationClient(p)} className="h-8 w-8 p-0 text-xs">
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button variant="outline" size="sm" onClick={() => handleExpPaginationClient(expPage + 1)} disabled={expPage === totalExpPages} className="h-8 text-xs">
                                                    {t("Next")}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t("No expense transactions")}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 3: PROFITABILITY ==================== */}
                <TabsContent value="profitability" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                        <Kpi label={t("Gross Revenue")} value={formatCurrency(profitability.summary.gross_revenue || 0)} icon={DollarSign} tone="green" />
                        <Kpi label={t("Total Expenses")} value={formatCurrency(profitability.summary.total_expenses || 0)} icon={CreditCard} tone="red" />
                        <Kpi label={t("Net Profit")} value={formatCurrency(profitability.summary.net_profit || 0)} icon={TrendingUp} tone={(profitability.summary.net_profit || 0) >= 0 ? "green" : "red"} />
                        <Kpi label={t("Profit Margin")} value={`${profitability.summary.profit_margin}%`} icon={Activity} tone={(profitability.summary.profit_margin || 0) >= 15 ? "green" : "red"} />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t("Revenue → Expenses → Profit")}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {profitability.waterfall && profitability.waterfall.length > 0 ? (
                                    (() => {
                                        const items = profitability.waterfall || [];
                                        const rev = Math.abs(items.find((i: any) => i.label?.includes("Revenue"))?.value || 0);
                                        const exp = Math.abs(items.find((i: any) => i.label?.includes("Expense"))?.value || 0);
                                        const prof = items.find((i: any) => i.label?.includes("Profit"))?.value || 0;
                                        return (
                                            <BarChart
                                                data={[
                                                    { name: t("Revenue"), Revenue: rev },
                                                    { name: t("Expenses"), Expenses: exp },
                                                    { name: t("Net Profit"), Profit: Math.abs(prof) },
                                                ]}
                                                bars={[
                                                    { dataKey: "Revenue", color: "#10b981", name: t("Revenue") },
                                                    { dataKey: "Expenses", color: "#ef4444", name: t("Expenses") },
                                                    { dataKey: "Profit", color: prof >= 0 ? "#10b981" : "#ef4444", name: t("Net Profit") },
                                                ]}
                                                xAxisKey="name"
                                                stacked
                                                showLegend
                                                height={250}
                                            />
                                        );
                                    })()
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t("No waterfall data")}</div>
                                )}
                            </CardContent>
                        </Card>
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t("Profit Trend (12 months)")}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {profitability.trend && profitability.trend.length > 0 ? (
                                    <AreaChart
                                        data={profitability.trend.map((p: any) => ({
                                            month: p.month,
                                            'Revenue': p.revenue || 0,
                                            'Expense': p.expense || 0
                                        }))}
                                        dataKey="Revenue"
                                        xAxisKey="month"
                                        color="#10b981"
                                        gradient
                                        height={250}
                                        areas={[
                                            { dataKey: "Revenue", color: "#10b981", name: t("Revenue") },
                                            { dataKey: "Expense", color: "#ef4444", name: t("Expense") },
                                        ]}
                                        stacked={false}
                                        showLegend
                                        showTooltip
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t("No profit trend data")}</div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-2 flex-wrap">
                            <CardTitle className="text-md">{t("Profitability Breakdown")}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap w-full sm:w-auto">
                                <div className="flex border rounded-md overflow-hidden">
                                    <Button variant={profitView === "type" ? "default" : "outline"} size="sm" className="rounded-none border-0 text-xs sm:text-sm" onClick={() => setProfitView("type")}>
                                        {t("By Type")}
                                    </Button>
                                    <Button variant={profitView === "account" ? "default" : "outline"} size="sm" className="rounded-none border-0 text-xs sm:text-sm" onClick={() => setProfitView("account")}>
                                        {t("By Account")}
                                    </Button>
                                    <Button variant={profitView === "month" ? "default" : "outline"} size="sm" className="rounded-none border-0 text-xs sm:text-sm" onClick={() => setProfitView("month")}>
                                        {t("By Month")}
                                    </Button>
                                </div>
                                <ExportButton 
                                    data={profitTableData.map((item: any) => ({
                                        ...item,
                                        revenue: item.revenue || 0,
                                        cost: item.cost || 0,
                                        expense: item.expense || 0,
                                        profit: item.profit || 0,
                                        amount: item.amount || 0,
                                        margin: item.margin || 0,
                                    }))} 
                                    columns={profitColumns.map(col => ({
                                        ...col,
                                        render: col.render ? col.render : undefined
                                    }))} 
                                    filename={`profitability-${profitView}`} 
                                    title={t("Profitability")} 
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {profitTableData.length > 0 ? (
                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                {profitColumns.map((col) => (
                                                    <TableHead key={col.key} className="cursor-pointer select-none hover:bg-muted/50 text-xs sm:text-sm" onClick={() => handleProfitSort(col.key)}>
                                                        <div className="flex items-center gap-1">
                                                            {col.header}
                                                            {getProfitSortIcon(col.key)}
                                                        </div>
                                                    </TableHead>
                                                ))}
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {profitTableData.map((item: any, idx: number) => (
                                                <TableRow key={idx} className="hover:bg-muted/50">
                                                    {profitColumns.map((col) => {
                                                        const cellValue = col.render ? col.render(item[col.key], item) : item[col.key] || "-";
                                                        const textAlign = ["profit", "cost", "amount", "revenue", "expense"].includes(col.key) ? "text-right" : "";
                                                        return (
                                                            <TableCell key={col.key} className={`text-xs sm:text-sm ${textAlign} ${["profit", "cost", "amount", "revenue", "expense"].includes(col.key) ? "font-medium" : ""}`}>
                                                                {cellValue}
                                                            </TableCell>
                                                        );
                                                    })}
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </div>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t("No data available")}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 4: CASH FLOW ==================== */}
                <TabsContent value="cashflow" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <Kpi label={t("Cash Balance")} value={formatCurrency(cash_flow.kpi.cash_balance || 0, pageProps)} icon={Wallet} tone="green" />
                        <Kpi label={t("Accounts Receivable")} value={formatCurrency(cash_flow.kpi.accounts_receivable || 0, pageProps)} icon={ArrowUpRight} tone="yellow" />
                        <Kpi label={t("Accounts Payable")} value={formatCurrency(cash_flow.kpi.accounts_payable || 0, pageProps)} icon={ArrowDownRight} tone="orange" />
                    </div>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-md">{t("90-Day Cash Flow Forecast")}</CardTitle>
                        </CardHeader>
                        <CardContent className="h-72">
                            {cash_flow.forecast && cash_flow.forecast.length > 0 ? (
                                <LineChart
                                    data={cash_flow.forecast.map((f: any) => ({
                                        date: f.journal_date,
                                        'Net Cash Flow': f.net_cash_flow || 0
                                    }))}
                                    dataKey="Net Cash Flow"
                                    xAxisKey="date"
                                    color="#06b6d4"
                                    showDots
                                    showTooltip
                                    height={250}
                                />
                            ) : (
                                <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t("No forecast data available")}</div>
                            )}
                        </CardContent>
                    </Card>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        {/* AR Aging */}
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between">
                                <CardTitle className="text-md">{t("Accounts Receivable Aging")}</CardTitle>
                                <ExportButton
                                    data={cash_flow.ar_aging || []}
                                    columns={[
                                        { key: "customer", header: t("Customer") },
                                        { key: "total_invoiced", header: t("Total Invoiced"), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: "total_returns", header: t("Returns & Credit Notes"), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: "net_invoiced", header: t("Net Invoiced"), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: "total_paid", header: t("Total Paid"), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: "balance", header: t("Balance (Outstanding)"), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: "due_date", header: t("Due Date") },
                                        { key: "days_overdue", header: t("Days Overdue"), render: (v: number) => { const d = Math.max(0, v || 0); return d > 0 ? `${d}d` : "-"; } },
                                    ]}
                                    filename="ar-aging"
                                    title={t("AR Aging")}
                                />
                            </CardHeader>
                            <CardContent className="p-0">
                                {cash_flow.ar_aging && cash_flow.ar_aging.length > 0 ? (
                                    <div className="overflow-x-auto max-h-[480px] overflow-y-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead>{t("Customer")}</TableHead>
                                                    <TableHead className="text-right">{t("Invoiced")}</TableHead>
                                                    <TableHead className="text-right">{t("Returns")}</TableHead>
                                                    <TableHead className="text-right">{t("Net")}</TableHead>
                                                    <TableHead className="text-right">{t("Paid")}</TableHead>
                                                    <TableHead className="text-right">{t("Balance")}</TableHead>
                                                    <TableHead>{t("Due Date")}</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {(() => {
                                                    const current: any[] = [];
                                                    const days1_30: any[] = [];
                                                    const days31_60: any[] = [];
                                                    const days61_90: any[] = [];
                                                    const days90plus: any[] = [];
                                                    (cash_flow.ar_aging || []).forEach((item: any) => {
                                                        const d = Math.max(0, item.days_overdue || 0);
                                                        if (d <= 0) current.push(item);
                                                        else if (d <= 30) days1_30.push(item);
                                                        else if (d <= 60) days31_60.push(item);
                                                        else if (d <= 90) days61_90.push(item);
                                                        else days90plus.push(item);
                                                    });
                                                    const buckets = [
                                                        { label: t("Current"), color: "bg-green-100 text-green-800", items: current },
                                                        { label: t("1-30 Days"), color: "bg-yellow-100 text-yellow-800", items: days1_30 },
                                                        { label: t("31-60 Days"), color: "bg-orange-100 text-orange-800", items: days31_60 },
                                                        { label: t("61-90 Days"), color: "bg-red-100 text-red-800", items: days61_90 },
                                                        { label: t("90+ Days"), color: "bg-red-200 text-red-900", items: days90plus },
                                                    ];
                                                    const rows: any[] = [];
                                                    buckets.forEach((bucket) => {
                                                        if (bucket.items.length > 0) {
                                                            rows.push(
                                                                <TableRow key={bucket.label} className={bucket.color}>
                                                                    <TableCell colSpan={8} className="text-xs font-semibold">
                                                                        {bucket.label} ({bucket.items.length})
                                                                    </TableCell>
                                                                </TableRow>
                                                            );
                                                            bucket.items.forEach((item: any, i: number) => {
                                                                rows.push(
                                                                    <TableRow key={`${bucket.label}-${i}`} className="hover:bg-muted/50">
                                                                        <TableCell className="text-xs">
                                                                            {pageProps?.auth?.user?.permissions?.includes("view-customer-detail-report") && item.customer_id ? (
                                                                                <Link href={route("account.reports.customer-detail", item.customer_id)} className="text-blue-600 hover:text-blue-700 font-medium">
                                                                                    {item.customer}
                                                                                </Link>
                                                                            ) : (
                                                                                item.customer || "-"
                                                                            )}
                                                                        </TableCell>
                                                                        <TableCell className="text-right text-xs font-medium">{formatCurrency(item.total_invoiced || 0, pageProps)}</TableCell>
                                                                        <TableCell className="text-right text-xs font-medium">{formatCurrency(item.total_returns || 0, pageProps)}</TableCell>
                                                                        <TableCell className="text-right text-xs font-medium">{formatCurrency(item.net_invoiced || 0, pageProps)}</TableCell>
                                                                        <TableCell className="text-right text-xs font-medium">{formatCurrency(item.total_paid || 0, pageProps)}</TableCell>
                                                                        <TableCell className="text-right text-xs font-medium">{formatCurrency(item.balance || 0, pageProps)}</TableCell>
                                                                        <TableCell className="text-xs">{formatDate(item.due_date, pageProps)}</TableCell>
                                                                    </TableRow>
                                                                );
                                                            });
                                                        }
                                                    });
                                                    return rows;
                                                })()}
                                            </TableBody>
                                        </Table>
                                    </div>
                                ) : (
                                    <div className="text-center py-8 text-muted-foreground text-sm">{t("No AR data")}</div>
                                )}
                            </CardContent>
                        </Card>

                        {/* AP Aging */}
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between">
                                <CardTitle className="text-md">{t("Accounts Payable Aging")}</CardTitle>
                                <ExportButton
                                    data={cash_flow.ap_aging || []}
                                    columns={[
                                        { key: "vendor", header: t("Vendor") },
                                        { key: "invoice_number", header: t("Bill #") },
                                        { key: "amount", header: t("Amount"), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: "due_date", header: t("Due Date") },
                                        { key: "days_until_due", header: t("Days Until Due"), render: (v: number) => `${v || 0}d` },
                                        { key: "status", header: t("Status") },
                                    ]}
                                    filename="ap-aging"
                                    title={t("AP Aging")}
                                />
                            </CardHeader>
                            <CardContent className="p-0">
                                {cash_flow.ap_aging && cash_flow.ap_aging.length > 0 ? (
                                    <div className="overflow-x-auto max-h-[480px] overflow-y-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead>{t("Bill #")}</TableHead>
                                                    <TableHead>{t("Vendor")}</TableHead>
                                                    <TableHead className="text-right">{t("Amount")}</TableHead>
                                                    <TableHead>{t("Due Date")}</TableHead>
                                                    <TableHead>{t("Due In")}</TableHead>
                                                    <TableHead>{t("Status")}</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {(() => {
                                                    const overdue90plus: any[] = [];
                                                    const overdue61_90: any[] = [];
                                                    const overdue31_60: any[] = [];
                                                    const overdue1_30: any[] = [];
                                                    const current: any[] = [];
                                                    const dueSoon: any[] = [];
                                                    (cash_flow.ap_aging || []).forEach((item: any) => {
                                                        const d = item.days_until_due || 0;
                                                        if (d < -90) overdue90plus.push(item);
                                                        else if (d < -60) overdue61_90.push(item);
                                                        else if (d < -30) overdue31_60.push(item);
                                                        else if (d < 0) overdue1_30.push(item);
                                                        else if (d === 0) current.push(item);
                                                        else dueSoon.push(item);
                                                    });
                                                    const buckets = [
                                                        { label: t("90+ Days Overdue"), color: "bg-red-200 text-red-900", items: overdue90plus },
                                                        { label: t("61-90 Days Overdue"), color: "bg-red-100 text-red-800", items: overdue61_90 },
                                                        { label: t("31-60 Days Overdue"), color: "bg-orange-100 text-orange-800", items: overdue31_60 },
                                                        { label: t("1-30 Days Overdue"), color: "bg-yellow-100 text-yellow-800", items: overdue1_30 },
                                                        { label: t("Due Today"), color: "bg-blue-100 text-blue-800", items: current },
                                                        { label: t("Upcoming"), color: "bg-green-100 text-green-800", items: dueSoon },
                                                    ];
                                                    const rows: any[] = [];
                                                    buckets.forEach((bucket) => {
                                                        if (bucket.items.length > 0) {
                                                            rows.push(
                                                                <TableRow key={bucket.label} className={bucket.color}>
                                                                    <TableCell colSpan={6} className="text-xs font-semibold">
                                                                        {bucket.label} ({bucket.items.length})
                                                                    </TableCell>
                                                                </TableRow>
                                                            );
                                                            bucket.items.forEach((item: any, i: number) => {
                                                                const d = item.days_until_due || 0;
                                                                const isOverdue = d < 0;
                                                                rows.push(
                                                                    <TableRow key={`${bucket.label}-${i}`} className="hover:bg-muted/50">
                                                                        <TableCell className="text-xs">
                                                                            {pageProps?.auth?.user?.permissions?.includes("view-purchase-invoices") && item.id ? (
                                                                                <span className="text-blue-600 hover:text-blue-700 font-medium cursor-pointer" onClick={() => router.get(route("purchase-invoices.show", item.id))}>
                                                                                    {item.invoice_number}
                                                                                </span>
                                                                            ) : (
                                                                                item.invoice_number
                                                                            )}
                                                                        </TableCell>
                                                                        <TableCell className="text-xs">
                                                                            {pageProps?.auth?.user?.permissions?.includes("view-vendor-detail-report") && item.vendor_id ? (
                                                                                <Link href={route("account.reports.vendor-detail", item.vendor_id)} className="text-blue-600 hover:text-blue-700 font-medium">
                                                                                    {item.vendor}
                                                                                </Link>
                                                                            ) : (
                                                                                item.vendor || "-"
                                                                            )}
                                                                        </TableCell>
                                                                        <TableCell className="text-right text-xs font-medium">{formatCurrency(item.amount || 0, pageProps)}</TableCell>
                                                                        <TableCell className="text-xs">{formatDate(item.due_date, pageProps)}</TableCell>
                                                                        <TableCell className="text-xs">{isOverdue ? `${Math.abs(d)}d ${t("overdue")}` : d === 0 ? t("Today") : `${d}d`}</TableCell>
                                                                        <TableCell>
                                                                            <span className={`px-2 py-0.5 rounded-full text-xs font-medium border ${
                                                                                isOverdue && d < -30 ? 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800' :
                                                                                isOverdue ? 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800' :
                                                                                d === 0 ? 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800' :
                                                                                'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800'
                                                                            }`}>
                                                                                {isOverdue ? t("Overdue") : d === 0 ? t("Due Today") : t("Pending")}
                                                                            </span>
                                                                        </TableCell>
                                                                    </TableRow>
                                                                );
                                                            });
                                                        }
                                                    });
                                                    return rows;
                                                })()}
                                            </TableBody>
                                        </Table>
                                    </div>
                                ) : (
                                    <div className="text-center py-8 text-muted-foreground text-sm">{t("No AP data")}</div>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>

                {/* ==================== TAB 5: JOURNAL ENTRIES AUDIT TRAIL ==================== */}
                <TabsContent value="journal" className="mt-0 space-y-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap">
                            <CardTitle className="text-md">{t("All Journal Entries")}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Button variant="outline" size="sm" className="h-9 gap-1.5 text-sm" onClick={() => setShowJournalFilters(!showJournalFilters)}>
                                    <Filter className="h-4 w-4" />
                                    {t("Filters")}
                                    {showJournalFilters ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}
                                </Button>
                                <Select value={journalPerPage} onValueChange={(v: string) => { setJournalPerPage(v); setJournalPage(1); }}>
                                    <SelectTrigger className="h-9 gap-1.5 text-sm w-32">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="10">{t("10 per page")}</SelectItem>
                                        <SelectItem value="20">{t("20 per page")}</SelectItem>
                                        <SelectItem value="50">{t("50 per page")}</SelectItem>
                                        <SelectItem value="100">{t("100 per page")}</SelectItem>
                                    </SelectContent>
                                </Select>
                                <ExportButton
                                    data={allJournalEntries.map((entry: any) => ({
                                        ...entry,
                                        total_debit: entry.total_debit || entry.debit || 0,
                                        total_credit: entry.total_credit || entry.credit || 0
                                    }))}
                                    columns={[
                                        { key: "journal_number", header: t("Journal #") },
                                        { key: "date", header: t("Date"), render: (v: string) => formatDate(v, pageProps) },
                                        { key: "reference_type", header: t("Reference"), render: (v: string) => sourceLabels[v] || v },
                                        { key: "description", header: t("Description") },
                                        { key: "total_debit", header: t("Debit"), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: "total_credit", header: t("Credit"), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: "status", header: t("Status") },
                                    ]}
                                    filename="journal-entries-audit-trail"
                                    title={t("Journal Entries Audit Trail")}
                                />
                            </div>
                        </CardHeader>
                        {showJournalFilters && (
                            <CardContent className="border-b p-4">
                                <div className="grid grid-cols-1 md:grid-cols-4 gap-3">
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Date From")}</label>
                                        <DatePicker value={journalFilters.date_from} onChange={(v: string) => handleJournalFiltersChange("date_from", v)} placeholder={t("Select date")} />
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Date To")}</label>
                                        <DatePicker value={journalFilters.date_to} onChange={(v: string) => handleJournalFiltersChange("date_to", v)} placeholder={t("Select date")} />
                                    </div>
                                    <div>
                                        <label className="text-sm font-medium mb-1 block">{t("Status")}</label>
                                        <Select value={journalFilters.status} onValueChange={(v: string) => handleJournalFiltersChange("status", v)}>
                                            <SelectTrigger className="h-10 w-full">
                                                <SelectValue placeholder={t("All")} />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="all">{t("All")}</SelectItem>
                                                <SelectItem value="posted">{t("Posted")}</SelectItem>
                                                <SelectItem value="draft">{t("Draft")}</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                    <div className="flex items-end gap-2">
                                        <Button className="h-10" size="sm" onClick={handleJournalSearch}>{t("Apply")}</Button>
                                        <Button className="h-10" variant="outline" size="sm" onClick={handleJournalReset}>{t("Reset")}</Button>
                                    </div>
                                </div>
                            </CardContent>
                        )}
                        <CardContent className="p-0">
                            {paginatedJournalEntries && paginatedJournalEntries.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead className="w-8"></TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleJournalSort("journal_number")}>
                                                        {t("Journal #")} {getJournalSortIcon("journal_number")}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleJournalSort("date")}>
                                                        {t("Date")} {getJournalSortIcon("date")}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleJournalSort("reference_type")}>
                                                        {t("Reference")} {getJournalSortIcon("reference_type")}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleJournalSort("description")}>
                                                        {t("Description")} {getJournalSortIcon("description")}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50 text-right" onClick={() => handleJournalSort("debit")}>
                                                        {t("Debit")} {getJournalSortIcon("debit")}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50 text-right" onClick={() => handleJournalSort("credit")}>
                                                        {t("Credit")} {getJournalSortIcon("credit")}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleJournalSort("status")}>
                                                        {t("Status")} {getJournalSortIcon("status")}
                                                    </TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {paginatedJournalEntries.map((entry: any) => (
                                                    <React.Fragment key={entry.id}>
                                                        <TableRow className="hover:bg-muted/50 cursor-pointer" onClick={() => setExpandedJournal(expandedJournal === entry.id ? null : entry.id)}>
                                                            <TableCell>{expandedJournal === entry.id ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}</TableCell>
                                                            <TableCell className="font-medium">{entry.journal_number}</TableCell>
                                                            <TableCell className="whitespace-nowrap">{formatDate(entry.date, pageProps)}</TableCell>
                                                            <TableCell>{sourceLabels[entry.reference_type] || entry.reference_type}</TableCell>
                                                            <TableCell className="max-w-[200px]">{entry.description || "-"}</TableCell>
                                                            <TableCell className="text-right font-medium">{formatCurrency((entry as any).total_debit || (entry as any).debit || 0, pageProps)}</TableCell>
                                                            <TableCell className="text-right font-medium">{formatCurrency((entry as any).total_credit || (entry as any).credit || 0, pageProps)}</TableCell>
                                                            <TableCell>
                                                                <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize ${getStatusBadgeStyle(entry.status)}`}>{t(entry.status)}</span>
                                                            </TableCell>
                                                        </TableRow>
                                                        {expandedJournal === entry.id && entry.items && entry.items.length > 0 && (
                                                            <TableRow>
                                                                <TableCell colSpan={9} className="p-0">
                                                                    <div className="bg-gray-50 p-4 border-t">
                                                                        <h4 className="text-sm font-semibold mb-2">{t("Account Breakdown")}</h4>
                                                                        <Table>
                                                                            <TableHeader>
                                                                                <TableRow>
                                                                                    <TableHead>{t("Account")}</TableHead>
                                                                                    <TableHead>{t("Code")}</TableHead>
                                                                                    <TableHead className="text-right">{t("Debit")}</TableHead>
                                                                                    <TableHead className="text-right">{t("Credit")}</TableHead>
                                                                                </TableRow>
                                                                            </TableHeader>
                                                                            <TableBody>
                                                                                {entry.items.map((item: any, idx: number) => (
                                                                                    <TableRow key={idx}>
                                                                                        <TableCell className="text-xs">{item.account_name}</TableCell>
                                                                                        <TableCell className="text-xs font-mono">{item.account_code}</TableCell>
                                                                                        <TableCell className="text-right text-xs font-medium">{formatCurrency(item.debit_amount || 0, pageProps)}</TableCell>
                                                                                        <TableCell className="text-right text-xs font-medium">{formatCurrency(item.credit_amount || 0, pageProps)}</TableCell>
                                                                                    </TableRow>
                                                                                ))}
                                                                                <TableRow className="font-semibold border-t-2">
                                                                                    <TableCell colSpan={2} className="text-xs">{t("Total")}</TableCell>
                                                                                    <TableCell className="text-right text-xs">{formatCurrency((entry as any).total_debit || (entry as any).debit || 0, pageProps)}</TableCell>
                                                                                    <TableCell className="text-right text-xs">{formatCurrency((entry as any).total_credit || (entry as any).credit || 0, pageProps)}</TableCell>
                                                                                </TableRow>
                                                                            </TableBody>
                                                                        </Table>
                                                                    </div>
                                                                </TableCell>
                                                            </TableRow>
                                                        )}
                                                    </React.Fragment>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t("Showing")} {journalFrom} {t("to")} {journalTo} {t("of")} {allJournalEntries.length} {t("results")}
                                        </span>
                                        {totalJournalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button variant="outline" size="sm" onClick={() => setJournalPage(journalPage - 1)} disabled={journalPage === 1} className="h-8 text-sm">
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t("Previous")}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getJournalPageNumbers().map((p, i) =>
                                                        typeof p === "number" ? (
                                                            <Button key={i} variant={journalPage === p ? "default" : "outline"} size="sm" onClick={() => setJournalPage(p)} className="h-8 w-8 p-0 text-xs">
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button variant="outline" size="sm" onClick={() => setJournalPage(journalPage + 1)} disabled={journalPage === totalJournalPages} className="h-8 text-xs">
                                                    {t("Next")}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t("No journal entries found")}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </AuthenticatedLayout>
    );
}