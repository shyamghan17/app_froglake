declare const route: (name: string, params?: any) => string;
import { Head, usePage, Link } from '@inertiajs/react';
import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { BarChart } from '@/components/charts/BarChart';
import { PieChart } from '@/components/charts/PieChart';
import { formatCurrency, formatDate } from '@/utils/helpers';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
    Package, ShoppingCart, Briefcase,
    ClipboardList, CreditCard,
    ChevronDown, ChevronUp, ChevronLeft, ChevronRight,
    DollarSign, TrendingUp, AlertCircle, Zap, Users, Clock
} from 'lucide-react';
import ExportButton from '../../Components/ExportButton';

interface OperationalAnalyticsProps extends Record<string, any> {
    inventory_management: {
        kpi: { total_products: number; total_stock_value: number; out_of_stock: number; active_warehouses: number };
        stock_by_category: Array<{ category_name: string; color: string; product_count: number; total_quantity: number; stock_value: number }>;
        stock_by_warehouse: Array<{ warehouse_name: string; city: string; product_count: number; total_units: number; stock_value: number }>;
        product_list: { data: Array<Record<string, any>>; current_page?: number; from?: number; to?: number; total?: number; last_page?: number; prev_page_url?: string | null; next_page_url?: string | null };
    };
    pos_analytics: {
        kpi: { today_revenue: number; today_transactions: number; avg_transaction_value: number; top_product_today: string | null };
        hourly_revenue: Array<{ sale_hour: number; transaction_count: number; revenue: number }>;
        week_comparison: Array<{ metric: string; this_week: number; last_week: number }>;
        top_products: Array<{ product_name: string; category_name: string; units_sold: number; revenue: number }>;
        top_selling_products: Array<{ id: number; product_name: string; sku: string; category_name: string; list_price: number; purchase_price: number; units_sold: number; revenue: number; cost: number; profit: number; margin_percentage: number }>;
        transactions: { data: Array<Record<string, any>>; current_page?: number; from?: number; to?: number; total?: number; last_page?: number; prev_page_url?: string | null; next_page_url?: string | null };
    };
    project_management: {
        kpi: { active_projects: number; completed_this_month: number; delayed_projects: number; budget_utilization: number };
        status_distribution: Array<{ status: string; project_count: number; total_budget: number }>;
        budget_vs_milestone: Array<Record<string, any>>;
        project_list: { data: Array<Record<string, any>>; current_page?: number; from?: number; to?: number; total?: number; last_page?: number; prev_page_url?: string | null; next_page_url?: string | null };
    };
    purchase_vendor_analytics: {
        kpi: { purchases_this_month: number; pending_bills: number; pending_bill_amount: number; overdue_bills: number; unique_vendors: number };
        monthly_trend: Array<{ month: string; invoice_count: number; subtotal_amount: number; total_tax: number; total_amount: number; paid_amount: number; outstanding_amount: number }>;
        status_distribution: Array<{ status: string; invoice_count: number; total_amount: number; outstanding: number }>;
        vendor_summary: Array<{ user_id: number; vendor_name: string; vendor_code: string; vendor_email: string; contact_person_name: string; contact_person_mobile: string; total_invoices: number; total_purchased: number; total_paid: number; total_outstanding: number; overdue_count: number; pending_count: number; last_purchase_date: string; avg_invoice_value: number }>;
        purchase_invoices: { data: Array<Record<string, any>>; current_page?: number; from?: number; to?: number; total?: number; last_page?: number; prev_page_url?: string | null; next_page_url?: string | null };
        expense_category_totals: Array<{ category_name: string; category_code: string; total_amount: number; expense_count: number }>;
        operational_expenses: { data: Array<Record<string, any>>; current_page?: number; from?: number; to?: number; total?: number; last_page?: number; prev_page_url?: string | null; next_page_url?: string | null };
    };
}

// Compact KPI card — smaller footprint than four stacked CardHeader/CardContent
// lines, so the stat row takes noticeably less vertical space on every tab.
function Kpi({ label, value, tone = 'default', icon: Icon }: { label: string; value: React.ReactNode; tone?: 'default' | 'green' | 'blue' | 'yellow' | 'red' | 'orange'; icon?: any }) {
    const toneClass: Record<string, string> = {
        default: 'text-foreground',
        green: 'text-green-600',
        blue: 'text-blue-600',
        yellow: 'text-yellow-600',
        red: 'text-red-600',
        orange: 'text-orange-600',
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

export default function OperationalAnalytics() {
    const { t } = useTranslation();
    const props = usePage<OperationalAnalyticsProps>().props;
    const { inventory_management, pos_analytics, project_management, purchase_vendor_analytics } = props;
    const pageProps = props as any;


    const [expandedPos, setExpandedPos] = useState<number | null>(null);
    const [posDetails, setPosDetails] = useState<Record<number, any>>({});
    const [loadingPosDetail, setLoadingPosDetail] = useState<number | null>(null);
    const [productPagePerPage, setProductPagePerPage] = useState('10');
    const [productPage, setProductPage] = useState(1);
    const [posPagePerPage, setPosPagePerPage] = useState('10');
    const [posPage, setPosPage] = useState(1);
    const [projectPagePerPage, setProjectPagePerPage] = useState('10');
    const [projectPage, setProjectPage] = useState(1);
    const [showProductFilters, setShowProductFilters] = useState(false);
    const [productSortField, setProductSortField] = useState('product_name');
    const [productSortDir, setProductSortDir] = useState<'asc' | 'desc'>('asc');
    const [productFilters, setProductFilters] = useState({ stock_status: 'all' });
    const [showPosFilters, setShowPosFilters] = useState(false);
    const [posSortField, setPosSortField] = useState('sale_number');
    const [posSortDir, setPosSortDir] = useState<'asc' | 'desc'>('desc');
    const [posFilters, setPositFilters] = useState({ status: 'all' });
    const [showProjectFilters, setShowProjectFilters] = useState(false);
    const [projectSortField, setProjectSortField] = useState('project_name');
    const [projectSortDir, setProjectSortDir] = useState<'asc' | 'desc'>('asc');
    const [projectFilters, setProjectFilters] = useState({ status: 'all' });
    const [purchasePagePerPage, setPurchasePagePerPage] = useState('10');
    const [purchasePage, setPurchasePage] = useState(1);
    const [showPurchaseFilters, setShowPurchaseFilters] = useState(false);
    const [purchaseSortField, setPurchaseSortField] = useState('invoice_date');
    const [purchaseSortDir, setPurchaseSortDir] = useState<'asc' | 'desc'>('desc');
    const [purchaseFilters, setPurchaseFilters] = useState({ status: 'all' });
    const [vendorSortField, setVendorSortField] = useState('total_purchased');
    const [vendorSortDir, setVendorSortDir] = useState<'asc' | 'desc'>('desc');
    const [expensePagePerPage, setExpensePagePerPage] = useState('10');
    const [expensePage, setExpensePage] = useState(1);
    const [showExpenseFilters, setShowExpenseFilters] = useState(false);
    const [expenseSortField, setExpenseSortField] = useState('expense_date');
    const [expenseSortDir, setExpenseSortDir] = useState<'asc' | 'desc'>('desc');
    const [expenseFilters, setExpenseFilters] = useState({ status: 'all' });

    const displayDate = (date?: string | null) => date ? formatDate(date, pageProps) : '-';

    const handleProductPerPageChange = (value: string) => {
        setProductPagePerPage(value);
        setProductPage(1);
    };
    const handlePosPerPageChange = (value: string) => {
        setPosPagePerPage(value);
        setPosPage(1);
    };
    const handleProjectPerPageChange = (value: string) => {
        setProjectPagePerPage(value);
        setProjectPage(1);
    };

    const sortRecords = (records: Array<Record<string, any>>, field: string, direction: 'asc' | 'desc') => {
        return [...records].sort((a: any, b: any) => {
            const aVal = a[field];
            const bVal = b[field];
            const aNum = Number(aVal);
            const bNum = Number(bVal);

            if (aVal !== null && bVal !== null && aVal !== '' && bVal !== '' && !Number.isNaN(aNum) && !Number.isNaN(bNum)) {
                return direction === 'asc' ? aNum - bNum : bNum - aNum;
            }

            return direction === 'asc'
                ? String(aVal ?? '').localeCompare(String(bVal ?? ''))
                : String(bVal ?? '').localeCompare(String(aVal ?? ''));
        });
    };

    const getSortIcon = (activeField: string, field: string, direction: 'asc' | 'desc') => {
        if (activeField !== field) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return direction === 'asc' ? <ChevronUp className="h-4 w-4 inline ml-1" /> : <ChevronDown className="h-4 w-4 inline ml-1" />;
    };



    const fetchPosDetail = async (posId: number) => {
        if (posDetails[posId]) return;
        setLoadingPosDetail(posId);
        try {
            const res = await fetch(route('smart-analytics.operations.pos-detail', posId), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                credentials: 'same-origin',
            });
            const data = await res.json();
            setPosDetails(prev => ({ ...prev, [posId]: data }));
        } catch (err) {
            console.error('Failed to fetch POS detail', err);
        } finally {
            setLoadingPosDetail(null);
        }
    };

    const handlePosExpand = (posId: number) => {
        if (expandedPos === posId) {
            setExpandedPos(null);
        } else {
            setExpandedPos(posId);
            fetchPosDetail(posId);
        }
    };





    const projectStatusColors: Record<string, string> = {
        Ongoing: '#3b82f6', Onhold: '#f59e0b', Finished: '#10b981',
    };

    const getPurchaseStatusColor = (status: string): string => {
        const colors: Record<string, string> = {
            paid: '#10b981', posted: '#3b82f6', partial: '#f59e0b',
            overdue: '#ef4444', draft: '#6b7280',
        };
        return colors[status] || '#6b7280';
    };

    const stockBadge = (status: string) => {
        switch (status) {
            case 'In Stock': return 'text-green-600 bg-green-100 border-green-200';
            case 'Low Stock': return 'text-yellow-600 bg-yellow-100 border-yellow-200';
            default: return 'text-red-600 bg-red-100 border-red-200';
        }
    };

    const getHealthStatusColor = (status: string): string => {
        const colors: Record<string, string> = {
            'On Track': '#10b981', 'At Risk': '#f59e0b', 'Delayed': '#ef4444',
        };
        return colors[status] || '#6b7280';
    };

    // Product table sorting & filtering
    const handleProductSort = (field: string) => {
        const newDir: 'asc' | 'desc' = productSortField === field && productSortDir === 'desc' ? 'asc' : 'desc';
        setProductSortField(field);
        setProductSortDir(newDir);
    };

    const getProductSortIcon = (field: string) => {
        return getSortIcon(productSortField, field, productSortDir);
    };

    const getSortedProductList = () => {
        const products = sortRecords(inventory_management.product_list.data || [], productSortField, productSortDir);
        if (productFilters.stock_status !== 'all') {
            return products.filter((p: any) => p.stock_status === productFilters.stock_status);
        }
        return products.slice((productPage - 1) * productPerPageNum, productPage * productPerPageNum);
    };

    const productPerPageNum = parseInt(productPagePerPage, 10);
    const productTotalPages = Math.ceil((inventory_management.product_list.total || 0) / productPerPageNum);
    const productFrom = inventory_management.product_list.total > 0 ? (productPage - 1) * productPerPageNum + 1 : 0;
    const productTo = Math.min(productPage * productPerPageNum, inventory_management.product_list.total);

    const getProductPageNumbers = () => {
        const totalPages = productTotalPages || 1;
        const p: (number | string)[] = [];
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (productPage > 3) p.push('...');
            const s = Math.max(2, productPage - 1);
            const e = Math.min(totalPages - 1, productPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (productPage < totalPages - 2) p.push('...');
            p.push(totalPages);
        }
        return p;
    };

    // POS table sorting & filtering
    const handlePosSort = (field: string) => {
        const newDir: 'asc' | 'desc' = posSortField === field && posSortDir === 'desc' ? 'asc' : 'desc';
        setPosSortField(field);
        setPosSortDir(newDir);
    };

    const getPosSortIcon = (field: string) => {
        return getSortIcon(posSortField, field, posSortDir);
    };

    const posPerPageNum = parseInt(posPagePerPage, 10);
    const posTotalPages = Math.ceil((pos_analytics.transactions.total || 0) / posPerPageNum);
    const posFrom = pos_analytics.transactions.total > 0 ? (posPage - 1) * posPerPageNum + 1 : 0;
    const posTo = Math.min(posPage * posPerPageNum, pos_analytics.transactions.total);

    const getSortedPosList = () => {
        const items = sortRecords(pos_analytics.transactions.data || [], posSortField, posSortDir);
        if (posFilters.status !== 'all') {
            return items.filter((item: any) => item.status === posFilters.status);
        }
        return items.slice((posPage - 1) * posPerPageNum, posPage * posPerPageNum);
    };

    const getPosPageNumbers = () => {
        const totalPages = posTotalPages || 1;
        const p: (number | string)[] = [];
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (posPage > 3) p.push('...');
            const s = Math.max(2, posPage - 1);
            const e = Math.min(totalPages - 1, posPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (posPage < totalPages - 2) p.push('...');
            p.push(totalPages);
        }
        return p;
    };

    // Project table sorting & filtering
    const handleProjectSort = (field: string) => {
        const newDir: 'asc' | 'desc' = projectSortField === field && projectSortDir === 'desc' ? 'asc' : 'desc';
        setProjectSortField(field);
        setProjectSortDir(newDir);
    };

    const getProjectSortIcon = (field: string) => {
        return getSortIcon(projectSortField, field, projectSortDir);
    };

    const projectPerPageNum = parseInt(projectPagePerPage, 10);
    const projectTotalPages = Math.ceil((project_management.project_list.total || 0) / projectPerPageNum);
    const projectFrom = project_management.project_list.total > 0 ? (projectPage - 1) * projectPerPageNum + 1 : 0;
    const projectTo = Math.min(projectPage * projectPerPageNum, project_management.project_list.total);

    const getSortedProjectList = () => {
        const projects = sortRecords(project_management.project_list.data || [], projectSortField, projectSortDir);
        if (projectFilters.status !== 'all') {
            return projects.filter((p: any) => p.status === projectFilters.status);
        }
        return projects.slice((projectPage - 1) * projectPerPageNum, projectPage * projectPerPageNum);
    };

    const getProjectPageNumbers = () => {
        const totalPages = projectTotalPages || 1;
        const p: (number | string)[] = [];
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (projectPage > 3) p.push('...');
            const s = Math.max(2, projectPage - 1);
            const e = Math.min(totalPages - 1, projectPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (projectPage < totalPages - 2) p.push('...');
            p.push(totalPages);
        }
        return p;
    };

    const handlePurchaseSort = (field: string) => {
        const newDir: 'asc' | 'desc' = purchaseSortField === field && purchaseSortDir === 'desc' ? 'asc' : 'desc';
        setPurchaseSortField(field);
        setPurchaseSortDir(newDir);
    };

    const getPurchaseSortIcon = (field: string) => getSortIcon(purchaseSortField, field, purchaseSortDir);

    const handlePurchasePerPageChange = (value: string) => {
        setPurchasePagePerPage(value);
        setPurchasePage(1);
    };

    const purchasePerPageNum = parseInt(purchasePagePerPage, 10);
    const purchaseTotalPages = Math.ceil((purchase_vendor_analytics.purchase_invoices.total || 0) / purchasePerPageNum);
    const purchaseFrom = purchase_vendor_analytics.purchase_invoices.total > 0 ? (purchasePage - 1) * purchasePerPageNum + 1 : 0;
    const purchaseTo = Math.min(purchasePage * purchasePerPageNum, purchase_vendor_analytics.purchase_invoices.total);

    const getSortedPurchaseInvoices = () => {
        const invoices = sortRecords(purchase_vendor_analytics.purchase_invoices.data || [], purchaseSortField, purchaseSortDir);
        let filtered = invoices;
        if (purchaseFilters.status !== 'all') {
            filtered = invoices.filter((inv: any) => inv.status === purchaseFilters.status);
        }
        return filtered.slice((purchasePage - 1) * purchasePerPageNum, purchasePage * purchasePerPageNum);
    };

    const getPurchasePageNumbers = () => {
        const totalPages = purchaseTotalPages || 1;
        const p: (number | string)[] = [];
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (purchasePage > 3) p.push('...');
            const s = Math.max(2, purchasePage - 1);
            const e = Math.min(totalPages - 1, purchasePage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (purchasePage < totalPages - 2) p.push('...');
            p.push(totalPages);
        }
        return p;
    };

    const handleVendorSort = (field: string) => {
        const newDir: 'asc' | 'desc' = vendorSortField === field && vendorSortDir === 'desc' ? 'asc' : 'desc';
        setVendorSortField(field);
        setVendorSortDir(newDir);
    };

    const getVendorSortIcon = (field: string) => getSortIcon(vendorSortField, field, vendorSortDir);

    const getSortedVendorSummary = () => sortRecords(purchase_vendor_analytics.vendor_summary || [], vendorSortField, vendorSortDir);

    const handleExpenseSort = (field: string) => {
        const newDir: 'asc' | 'desc' = expenseSortField === field && expenseSortDir === 'desc' ? 'asc' : 'desc';
        setExpenseSortField(field);
        setExpenseSortDir(newDir);
    };

    const getExpenseSortIcon = (field: string) => getSortIcon(expenseSortField, field, expenseSortDir);

    const handleExpensePerPageChange = (value: string) => {
        setExpensePagePerPage(value);
        setExpensePage(1);
    };

    const expensePerPageNum = parseInt(expensePagePerPage, 10);
    const expenseTotalPages = Math.ceil((purchase_vendor_analytics.operational_expenses.total || 0) / expensePerPageNum);
    const expenseFrom = purchase_vendor_analytics.operational_expenses.total > 0 ? (expensePage - 1) * expensePerPageNum + 1 : 0;
    const expenseTo = Math.min(expensePage * expensePerPageNum, purchase_vendor_analytics.operational_expenses.total);

    const getSortedExpenses = () => {
        const expenses = sortRecords(purchase_vendor_analytics.operational_expenses.data || [], expenseSortField, expenseSortDir);
        let filtered = expenses;
        if (expenseFilters.status !== 'all') {
            filtered = expenses.filter((exp: any) => exp.status === expenseFilters.status);
        }
        return filtered.slice((expensePage - 1) * expensePerPageNum, expensePage * expensePerPageNum);
    };

    const getExpensePageNumbers = () => {
        const totalPages = expenseTotalPages || 1;
        const p: (number | string)[] = [];
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (expensePage > 3) p.push('...');
            const s = Math.max(2, expensePage - 1);
            const e = Math.min(totalPages - 1, expensePage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (expensePage < totalPages - 2) p.push('...');
            p.push(totalPages);
        }
        return p;
    };


    // Tab definitions used to render the nav strip — keeping this as data
    // means adding/removing a section never requires touching markup twice.
    const tabs = [
        { value: 'inventory', label: t('Inventory & Stock'), icon: Package },
        { value: 'pos', label: t('POS Analytics'), icon: ShoppingCart },
        { value: 'projects', label: t('Projects'), icon: Briefcase },
        { value: 'purchase', label: t('Purchase & Vendor'), icon: CreditCard },
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Smart Dashboard'), url: route('smart-analytics.dashboard') },
                { label: t('Operational Analytics') }
            ]}
            pageTitle={t('Operational Analytics')}
        >
            <Head title={t('Operational Analytics')} />

            <Tabs defaultValue="inventory" className="w-full">
                {/* Sticky tab strip: one section is visible at a time, so the
                   page that used to be 4 screens tall is now a single
                   screen's worth of content per tab. */}
                <TabsList className="sticky top-0 z-10 mb-6 h-auto w-full flex-wrap justify-start gap-1 overflow-x-auto bg-muted/60 p-1.5 backdrop-blur supports-[backdrop-filter]:bg-muted/50">
                    {tabs.map(({ value, label, icon: Icon }) => (
                        <TabsTrigger key={value} value={value} className="gap-1.5 whitespace-nowrap text-xs sm:text-sm">
                            <Icon className="h-3.5 w-3.5" />
                            {label}
                        </TabsTrigger>
                    ))}
                </TabsList>

                {/* ==================== TAB 1: INVENTORY & STOCK MANAGEMENT ==================== */}
                <TabsContent value="inventory" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                        <Kpi label={t('Total Products')} value={inventory_management.kpi.total_products} icon={Package} tone="blue" />
                        <Kpi label={t('Stock Value')} value={formatCurrency(inventory_management.kpi.total_stock_value || 0, pageProps)} icon={DollarSign} tone="green" />
                        <Kpi label={t('Out of Stock')} value={inventory_management.kpi.out_of_stock} icon={AlertCircle} tone="red" />
                        <Kpi label={t('Active Warehouses')} value={inventory_management.kpi.active_warehouses} icon={ClipboardList} tone="orange" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2"><CardTitle className="text-md">{t('Stock by Category')}</CardTitle></CardHeader>
                            <CardContent className="h-75">
                                {inventory_management.stock_by_category && inventory_management.stock_by_category.length > 0 ? (
                                    <PieChart
                                        data={inventory_management.stock_by_category
                                            .filter((c: any) => parseFloat(c.stock_value || 0) > 0)
                                            .map((c: any, i: number) => ({
                                                name: c.category_name,
                                                value: parseFloat(c.stock_value),
                                                color: c.color || ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316', '#84cc16', '#06b6d4'][i % 10]
                                            }))}
                                        dataKey="value" nameKey="name" height={350} donut showLegend showTooltip separatorNone
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
                                            height={350}
                                        />
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                        <Card>
                            <CardHeader className="pb-2"><CardTitle className="text-md">{t('Stock by Warehouse')}</CardTitle></CardHeader>
                            <CardContent className="h-75">
                                {inventory_management.stock_by_warehouse && inventory_management.stock_by_warehouse.length > 0 ? (
                                    <BarChart
                                        data={inventory_management.stock_by_warehouse.map((w: any) => ({
                                            warehouse_name: w.warehouse_name,
                                            'Stock Value': parseFloat(w.stock_value) || 0,
                                            product_count: w.product_count,
                                            total_units: w.total_units,
                                            city: w.city
                                        }))}
                                        dataKey="Stock Value"
                                        xAxisKey="warehouse_name"
                                        color="#3b82f6"
                                        height={350}
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t('No warehouse data')}</div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap pb-2">
                            <CardTitle className="text-md">{t('Complete Product Stock List')}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Select value={productPagePerPage} onValueChange={handleProductPerPageChange}>
                                    <SelectTrigger className="h-9 gap-1.5 text-sm w-32">
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
                                    data={getSortedProductList()}
                                    columns={[
                                        { key: 'product_name', header: t('Product') },
                                        { key: 'sku', header: t('SKU') },
                                        { key: 'category_name', header: t('Category') },
                                        { key: 'type', header: t('Type') },
                                        { key: 'unit', header: t('Unit') },
                                        { key: 'purchase_price', header: t('Purchase Price'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'sale_price', header: t('Sale Price'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'margin', header: t('Margin'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'total_stock', header: t('Stock') },
                                        { key: 'stock_value', header: t('Value'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'stock_status', header: t('Status') },
                                    ]}
                                    filename="product-stock-list"
                                    title={t('Complete Product Stock List')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {inventory_management.product_list.data && inventory_management.product_list.data.length > 0 ? (
                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProductSort('product_name')}>{t('Product')} {getProductSortIcon('product_name')}</TableHead>
                                                <TableHead>{t('Category')}</TableHead>
                                                <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProductSort('type')}>{t('Type')} {getProductSortIcon('type')}</TableHead>
                                                <TableHead>{t('Unit')}</TableHead>
                                                <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProductSort('purchase_price')}>{t('Purchase Price')} {getProductSortIcon('purchase_price')}</TableHead>
                                                <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProductSort('sale_price')}>{t('Sale Price')} {getProductSortIcon('sale_price')}</TableHead>
                                                <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProductSort('margin')}>{t('Margin')} {getProductSortIcon('margin')}</TableHead>
                                                <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProductSort('total_stock')}>{t('Stock')} {getProductSortIcon('total_stock')}</TableHead>
                                                <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProductSort('stock_value')}>{t('Value')} {getProductSortIcon('stock_value')}</TableHead>
                                                <TableHead>{t('Status')}</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {getSortedProductList().map((product: any) => (
                                                <TableRow key={product.id} className="hover:bg-muted/50">
                                                    <TableCell>
                                                        <div className="flex flex-col">
                                                            <span className="font-medium">{product.product_name}</span>
                                                            <span className="text-xs text-muted-foreground">
                                                                {product.sku}
                                                            </span>
                                                        </div>
                                                    </TableCell>
                                                    <TableCell>
                                                        {product.category_name ? (
                                                            <span className="text-xs px-1.5 py-0.5 rounded-full"
                                                                style={{ backgroundColor: product.category_color + '20', color: product.category_color, border: `1px solid ${product.category_color}` }}>
                                                                {product.category_name}
                                                            </span>
                                                        ) : '-'}
                                                    </TableCell>
                                                    <TableCell className="text-xs capitalize">{product.type}</TableCell>
                                                    <TableCell className="text-xs">{product.unit || '-'}</TableCell>
                                                    <TableCell className="text-right">{formatCurrency(product.purchase_price, pageProps)}</TableCell>
                                                    <TableCell className="text-right">{formatCurrency(product.sale_price, pageProps)}</TableCell>
                                                    <TableCell className="text-right">
                                                        <span className={product.margin >= 0 ? 'text-green-600' : 'text-red-600'}>
                                                            {formatCurrency(product.margin, pageProps)}
                                                            <span className="text-xs ml-1">({product.margin_percentage}%)</span>
                                                        </span>
                                                    </TableCell>
                                                    <TableCell className="text-right">{product.total_stock}</TableCell>
                                                    <TableCell className="text-right">{formatCurrency(product.stock_value || 0, pageProps)}</TableCell>
                                                    <TableCell>
                                                        <span className={`text-xs px-2 py-0.5 rounded-full border ${stockBadge(product.stock_status)}`}>
                                                            {product.stock_status}
                                                        </span>
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t('Showing')} {productFrom} {t('to')} {productTo} {t('of')} {inventory_management.product_list.total} {t('results')}
                                        </span>
                                        {productTotalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button variant="outline" size="sm" onClick={() => setProductPage(productPage - 1)} disabled={productPage === 1} className="h-8 text-sm">
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t('Previous')}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getProductPageNumbers().map((p, i) =>
                                                        typeof p === 'number' ? (
                                                            <Button key={i} variant={productPage === p ? 'default' : 'outline'} size="sm" onClick={() => setProductPage(p)} className="h-8 w-8 p-0 text-xs">
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button variant="outline" size="sm" onClick={() => setProductPage(productPage + 1)} disabled={productPage === productTotalPages} className="h-8 text-xs">
                                                    {t('Next')}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No product data')}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 2: POS ANALYTICS ==================== */}
                <TabsContent value="pos" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                        <Kpi label={t("Today's Revenue")} value={formatCurrency(pos_analytics.kpi.today_revenue || 0, pageProps)} icon={DollarSign} tone="green" />
                        <Kpi label={t('Today Transactions')} value={pos_analytics.kpi.today_transactions} icon={ShoppingCart} tone="blue" />
                        <Kpi label={t('Avg Transaction')} value={formatCurrency(pos_analytics.kpi.avg_transaction_value || 0, pageProps)} icon={TrendingUp} tone="orange" />
                        <Kpi label={t('Top Product Today')} value={pos_analytics.kpi.top_product_today || '-'} icon={Zap} tone="yellow" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2"><CardTitle className="text-md">{t('Week Comparison (Revenue vs Returns)')}</CardTitle></CardHeader>
                            <CardContent className="h-72">
                                {pos_analytics.week_comparison && pos_analytics.week_comparison.length > 0 ? (
                                    <BarChart
                                        data={pos_analytics.week_comparison.map((w: any) => ({
                                            metric: w.metric,
                                            'This Week': parseFloat(w.this_week) || 0,
                                            'Last Week': parseFloat(w.last_week) || 0
                                        }))}
                                        dataKey="This Week"
                                        xAxisKey="metric"
                                        color="#3b82f6"
                                        height={250}
                                        bars={[
                                            { dataKey: 'This Week', color: '#3b82f6', name: t('This Week') },
                                            { dataKey: 'Last Week', color: '#10b981', name: t('Last Week') },
                                        ]}
                                        showLegend
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t('No comparison data')}</div>
                                )}
                            </CardContent>
                        </Card>
                        <Card>
                            <CardHeader className="pb-2"><CardTitle className="text-md">{t('Top Products This Week')}</CardTitle></CardHeader>
                            <CardContent className="h-72">
                                {pos_analytics.top_products && pos_analytics.top_products.length > 0 ? (
                                    <BarChart
                                        data={pos_analytics.top_products.map((p: any) => ({
                                            product_name: p.product_name,
                                            'Revenue': parseFloat(p.revenue) || 0,
                                            'Units Sold': parseInt(p.units_sold) || 0,
                                            category_name: p.category_name
                                        }))}
                                        dataKey="Revenue"
                                        xAxisKey="product_name"
                                        color="#8b5cf6"
                                        height={250}
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t('No product data')}</div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap pb-2">
                            <CardTitle className="text-md">{t('POS Transactions')}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Select value={posPagePerPage} onValueChange={handlePosPerPageChange}>
                                    <SelectTrigger className="h-9 gap-1.5 text-sm w-32">
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
                                    data={getSortedPosList()}
                                    columns={[
                                        { key: 'sale_number', header: t('Sale #') },
                                        { key: 'pos_date', header: t('Date'), render: (v: string) => displayDate(v) },
                                        { key: 'customer_name', header: t('Customer') },
                                        { key: 'warehouse_name', header: t('Warehouse') },
                                        { key: 'items_count', header: t('Items') },
                                        { key: 'total_paid', header: t('Total'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'status', header: t('Status') },
                                    ]}
                                    filename="pos-transactions"
                                    title={t('POS Transactions')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {pos_analytics.transactions.data && pos_analytics.transactions.data.length > 0 ? (
                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead className="w-8"></TableHead>
                                                <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePosSort('sale_number')}>{t('Sale #')} {getPosSortIcon('sale_number')}</TableHead>
                                                <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePosSort('pos_date')}>{t('Date')} {getPosSortIcon('pos_date')}</TableHead>
                                                <TableHead>{t('Customer')}</TableHead>
                                                <TableHead>{t('Warehouse')}</TableHead>
                                                <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePosSort('items_count')}>{t('Items')} {getPosSortIcon('items_count')}</TableHead>
                                                <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePosSort('total_paid')}>{t('Total')} {getPosSortIcon('total_paid')}</TableHead>
                                                <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePosSort('status')}>{t('Status')} {getPosSortIcon('status')}</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {getSortedPosList().map((txn: any, idx: number) => (
                                                <React.Fragment key={idx}>
                                                    <TableRow className="hover:bg-muted/50 cursor-pointer"
                                                        onClick={() => handlePosExpand(txn.id)}>
                                                        <TableCell>{expandedPos === txn.id ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}</TableCell>
                                                        {/* <TableCell className="font-medium">{txn.sale_number}</TableCell> */}
                                                        <TableCell className="text-sm">
                                                            {pageProps?.auth?.user?.permissions?.includes("view-pos-orders") && txn.id ? (
                                                                <Link href={route("pos.show", txn.id)} className="text-blue-600 hover:text-blue-700 font-medium">
                                                                    {txn.sale_number}
                                                                </Link>
                                                            ) : (
                                                                txn.sale_number || "-"
                                                            )}
                                                        </TableCell>
                                                        <TableCell className="text-muted-foreground">{displayDate(txn.pos_date)}</TableCell>
                                                        <TableCell className="max-w-[150px]">{txn.customer_name}</TableCell>
                                                        <TableCell className="text-muted-foreground">{txn.warehouse_name || ''}</TableCell>
                                                        <TableCell className="text-right">{txn.items_count}</TableCell>
                                                        <TableCell className="text-right font-medium">{formatCurrency(txn.total_paid || 0, pageProps)}</TableCell>
                                                        <TableCell>
                                                            <span className={`text-xs px-2 py-0.5 rounded-full border capitalize ${txn.status === 'completed' ? 'text-green-600 bg-green-100 border-green-200' :
                                                                txn.status === 'pending' ? 'text-yellow-600 bg-yellow-100 border-yellow-200' :
                                                                    'text-red-600 bg-red-100 border-red-200'
                                                                }`}>{txn.status}</span>
                                                        </TableCell>
                                                    </TableRow>
                                                    {expandedPos === txn.id && (
                                                        <TableRow>
                                                            <TableCell colSpan={8} className="p-4 bg-gray-50 border-t">
                                                                {loadingPosDetail === txn.id ? (
                                                                    <div className="text-center py-4 text-sm text-muted-foreground">{t('Loading...')}</div>
                                                                ) : posDetails[txn.id]?.items ? (
                                                                    <div className="space-y-1">
                                                                        <div>
                                                                            <h4 className="text-sm font-semibold mb-1">{t('Line Items')}</h4>
                                                                            <Table>
                                                                                <TableHeader>
                                                                                    <TableRow>
                                                                                        <TableHead>{t('Product')}</TableHead>
                                                                                        <TableHead>{t('SKU')}</TableHead>
                                                                                        <TableHead className="text-right">{t('Qty')}</TableHead>
                                                                                        <TableHead className="text-right">{t('Unit Price')}</TableHead>
                                                                                        <TableHead className="text-right">{t('Subtotal')}</TableHead>
                                                                                        <TableHead className="text-right">{t('Tax')}</TableHead>
                                                                                        <TableHead className="text-right">{t('Total')}</TableHead>
                                                                                    </TableRow>
                                                                                </TableHeader>
                                                                                <TableBody>
                                                                                    {posDetails[txn.id].items.map((item: any, i: number) => (
                                                                                        <TableRow key={i}>
                                                                                            <TableCell className="text-xs">{item.product_name}</TableCell>
                                                                                            <TableCell className="text-xs text-muted-foreground">{item.sku}</TableCell>
                                                                                            <TableCell className="text-right text-xs">{item.quantity}</TableCell>
                                                                                            <TableCell className="text-right text-xs">{formatCurrency(item.unit_price, pageProps)}</TableCell>
                                                                                            <TableCell className="text-right text-xs">{formatCurrency(item.subtotal, pageProps)}</TableCell>
                                                                                            <TableCell className="text-right text-xs">{formatCurrency(item.tax_amount, pageProps)}</TableCell>
                                                                                            <TableCell className="text-right text-xs">{formatCurrency(item.total_amount, pageProps)}</TableCell>
                                                                                        </TableRow>
                                                                                    ))}
                                                                                </TableBody>
                                                                            </Table>
                                                                        </div>
                                                                    </div>
                                                                ) : (
                                                                    <div className="text-center py-4 text-sm text-muted-foreground">{t('No items found')}</div>
                                                                )}
                                                            </TableCell>
                                                        </TableRow>
                                                    )}
                                                </React.Fragment>
                                            ))}
                                        </TableBody>
                                    </Table>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t('Showing')} {posFrom} {t('to')} {posTo} {t('of')} {pos_analytics.transactions.total} {t('results')}
                                        </span>
                                        {posTotalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button variant="outline" size="sm" onClick={() => setPosPage(posPage - 1)} disabled={posPage === 1} className="h-8 text-sm">
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t('Previous')}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getPosPageNumbers().map((p, i) =>
                                                        typeof p === 'number' ? (
                                                            <Button key={i} variant={posPage === p ? 'default' : 'outline'} size="sm" onClick={() => setPosPage(p)} className="h-8 w-8 p-0 text-xs">
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button variant="outline" size="sm" onClick={() => setPosPage(posPage + 1)} disabled={posPage === posTotalPages} className="h-8 text-xs">
                                                    {t('Next')}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No transactions')}</div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2"><CardTitle className="text-md">{t('Top Selling Products')}</CardTitle></CardHeader>
                        <CardContent className="p-0">
                            {pos_analytics.top_selling_products && pos_analytics.top_selling_products.length > 0 ? (
                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>{t('Product')}</TableHead>
                                                <TableHead>{t('SKU')}</TableHead>
                                                <TableHead>{t('Category')}</TableHead>
                                                <TableHead className="text-right">{t('Units Sold')}</TableHead>
                                                <TableHead className="text-right">{t('Revenue')}</TableHead>
                                                <TableHead className="text-right">{t('Cost')}</TableHead>
                                                <TableHead className="text-right">{t('Profit')}</TableHead>
                                                <TableHead className="text-right">{t('Margin')}</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {pos_analytics.top_selling_products.map((prod: any, idx: number) => (
                                                <TableRow key={idx} className="hover:bg-muted/50">
                                                    <TableCell className="font-medium">{prod.product_name}</TableCell>
                                                    <TableCell className="text-xs text-muted-foreground">{prod.sku}</TableCell>
                                                    <TableCell className="text-xs">{prod.category_name || ''}</TableCell>
                                                    <TableCell className="text-right">{prod.units_sold}</TableCell>
                                                    <TableCell className="text-right">{formatCurrency(prod.revenue, pageProps)}</TableCell>
                                                    <TableCell className="text-right">{formatCurrency(prod.cost, pageProps)}</TableCell>
                                                    <TableCell className="text-right text-green-600">{formatCurrency(prod.profit, pageProps)}</TableCell>
                                                    <TableCell className="text-right">{prod.margin_percentage}%</TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </div>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No product data')}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 3: PROJECT MANAGEMENT ==================== */}
                <TabsContent value="projects" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                        <Kpi label={t('Active Projects')} value={project_management.kpi.active_projects} icon={Briefcase} tone="blue" />
                        <Kpi label={t('Project Completed (Month)')} value={project_management.kpi.completed_this_month} icon={TrendingUp} tone="green" />
                        <Kpi label={t('Project Delayed')} value={project_management.kpi.delayed_projects} icon={AlertCircle} tone="red" />
                        <Kpi label={t('Budget Utilization')} value={`${project_management.kpi.budget_utilization}%`} icon={DollarSign} tone="orange" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2"><CardTitle className="text-md">{t('Project Status Distribution')}</CardTitle></CardHeader>
                            <CardContent className="h-72">
                                {project_management.status_distribution && project_management.status_distribution.length > 0 ? (
                                    <PieChart
                                        data={project_management.status_distribution.map(s => ({
                                            ...s, name: s.status, color: projectStatusColors[s.status] || '#6b7280'
                                        }))}
                                        dataKey="project_count" nameKey="name" donut showLegend showTooltip height={250}
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
                            <CardHeader className="pb-2"><CardTitle className="text-md">{t('Budget vs Milestone Cost')}</CardTitle></CardHeader>
                            <CardContent className="h-72">
                                {project_management.budget_vs_milestone && project_management.budget_vs_milestone.length > 0 ? (
                                    <BarChart
                                        data={project_management.budget_vs_milestone.slice(0, 10).map((p: any) => ({
                                            project_name: p.project_name,
                                            'Budget': parseFloat(p.budget) || 0,
                                            'Milestone Cost': parseFloat(p.milestone_cost) || 0
                                        }))}
                                        dataKey="Budget"
                                        xAxisKey="project_name"
                                        color="#3b82f6"
                                        height={250}
                                        bars={[
                                            { dataKey: 'Budget', color: '#3b82f6', name: t('Budget') },
                                            { dataKey: 'Milestone Cost', color: '#f59e0b', name: t('Milestone Cost') },
                                        ]}
                                        showLegend
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t('No budget data')}</div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap pb-2">
                            <CardTitle className="text-md">{t('All Project List')}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Select value={projectPagePerPage} onValueChange={handleProjectPerPageChange}>
                                    <SelectTrigger className="h-9 gap-1.5 text-sm w-32">
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
                                    data={getSortedProjectList()}
                                    columns={[
                                        { key: 'project_name', header: t('Project') },
                                        { key: 'status', header: t('Status') },
                                        { key: 'start_date', header: t('Start Date'), render: (v: string) => displayDate(v) },
                                        { key: 'end_date', header: t('End Date'), render: (v: string) => displayDate(v) },
                                        { key: 'budget', header: t('Budget'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'milestone_cost', header: t('Milestone Cost'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'health_status', header: t('Health Status') },
                                    ]}
                                    filename="project-list"
                                    title={t('Complete Project List')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {project_management.project_list.data && project_management.project_list.data.length > 0 ? (
                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProjectSort('project_name')}>{t('Project')} {getProjectSortIcon('project_name')}</TableHead>
                                                <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProjectSort('status')}>{t('Status')} {getProjectSortIcon('status')}</TableHead>
                                                <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProjectSort('start_date')}>{t('Start Date')} {getProjectSortIcon('start_date')}</TableHead>
                                                <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProjectSort('end_date')}>{t('End Date')} {getProjectSortIcon('end_date')}</TableHead>
                                                <TableHead className="text-center">{t('Progress')}</TableHead>
                                                <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProjectSort('budget')}>{t('Budget')} {getProjectSortIcon('budget')}</TableHead>
                                                <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProjectSort('milestone_cost')}>{t('Milestone Cost')} {getProjectSortIcon('milestone_cost')}</TableHead>
                                                <TableHead className="text-right">{t('Variance')}</TableHead>
                                                <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProjectSort('health_status')}>{t('Health')} {getProjectSortIcon('health_status')}</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {getSortedProjectList().map((proj: any) => {
                                                const totalTasks = proj.total_tasks || 0;
                                                const completedTasks = proj.completed_tasks || 0;
                                                const progressPct = totalTasks > 0 ? Math.round((completedTasks / totalTasks) * 100) : 0;
                                                const variance = (proj.budget || 0) - (proj.milestone_cost || 0);
                                                return (
                                                    <TableRow key={proj.id} className="hover:bg-muted/50">
                                                        <TableCell className="font-medium">{proj.project_name}</TableCell>
                                                        <TableCell>
                                                            <span className={`text-xs px-2 py-0.5 rounded-full border capitalize ${proj.status === 'Finished' ? 'text-green-600 bg-green-100 border-green-200' :
                                                                proj.status === 'Ongoing' ? 'text-blue-600 bg-blue-100 border-blue-200' :
                                                                    'text-yellow-600 bg-yellow-100 border-yellow-200'
                                                                }`}>{proj.status}</span>
                                                        </TableCell>
                                                        <TableCell className="text-sm text-muted-foreground">{displayDate(proj.start_date)}</TableCell>
                                                        <TableCell className="text-sm text-muted-foreground">{displayDate(proj.end_date)}</TableCell>
                                                        <TableCell className="text-center">
                                                            <div className="flex flex-col items-center gap-0.5">
                                                                <span className="text-xs font-medium" style={{
                                                                    color: progressPct >= 75 ? '#10b981' : progressPct >= 50 ? '#f59e0b' : '#ef4444'
                                                                }}>{progressPct}%</span>
                                                                <div className="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                                    <div className="h-full" style={{
                                                                        width: `${progressPct}%`,
                                                                        backgroundColor: progressPct >= 75 ? '#10b981' : progressPct >= 50 ? '#f59e0b' : '#ef4444'
                                                                    }}></div>
                                                                </div>
                                                            </div>
                                                        </TableCell>
                                                        <TableCell className="text-right">{formatCurrency(proj.budget || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right">{formatCurrency(proj.milestone_cost || 0, pageProps)}</TableCell>
                                                        <TableCell className={`text-right font-medium ${variance >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                                                            {formatCurrency(variance, pageProps)}
                                                        </TableCell>
                                                        <TableCell>
                                                            <span className="text-xs px-2 py-0.5 rounded-full border"
                                                                style={{ backgroundColor: getHealthStatusColor(proj.health_status) + '20', color: getHealthStatusColor(proj.health_status), borderColor: getHealthStatusColor(proj.health_status) }}>
                                                                {proj.health_status}
                                                            </span>
                                                        </TableCell>
                                                    </TableRow>
                                                );
                                            })}
                                        </TableBody>
                                    </Table>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t('Showing')} {projectFrom} {t('to')} {projectTo} {t('of')} {project_management.project_list.total} {t('results')}
                                        </span>
                                        {projectTotalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button variant="outline" size="sm" onClick={() => setProjectPage(projectPage - 1)} disabled={projectPage === 1} className="h-8 text-sm">
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t('Previous')}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getProjectPageNumbers().map((p, i) =>
                                                        typeof p === 'number' ? (
                                                            <Button key={i} variant={projectPage === p ? 'default' : 'outline'} size="sm" onClick={() => setProjectPage(p)} className="h-8 w-8 p-0 text-xs">
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button variant="outline" size="sm" onClick={() => setProjectPage(projectPage + 1)} disabled={projectPage === projectTotalPages} className="h-8 text-xs">
                                                    {t('Next')}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No project data')}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 4: PURCHASE & VENDOR ANALYTICS ==================== */}
                <TabsContent value="purchase" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2 sm:gap-3">
                        <Kpi label={t('Purchases (Month)')} value={formatCurrency(purchase_vendor_analytics.kpi.purchases_this_month || 0, pageProps)} icon={ShoppingCart} tone="blue" />
                        <Kpi label={t('Pending Bills')} value={purchase_vendor_analytics.kpi.pending_bills} icon={Clock} tone="yellow" />
                        <Kpi label={t('Pending Amount')} value={formatCurrency(purchase_vendor_analytics.kpi.pending_bill_amount || 0, pageProps)} icon={DollarSign} tone="orange" />
                        <Kpi label={t('Overdue Bills')} value={purchase_vendor_analytics.kpi.overdue_bills} icon={AlertCircle} tone="red" />
                        <Kpi label={t('Total Vendors')} value={purchase_vendor_analytics.kpi.unique_vendors} icon={Users} tone="green" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2"><CardTitle className="text-md">{t('Monthly Purchase Trend')}</CardTitle></CardHeader>
                            <CardContent className="h-72">
                                {purchase_vendor_analytics.monthly_trend && purchase_vendor_analytics.monthly_trend.length > 0 ? (
                                    <BarChart
                                        data={purchase_vendor_analytics.monthly_trend.map((m: any) => ({
                                            month: m.month,
                                            'Paid Amount': parseFloat(m.paid_amount) || 0,
                                            'Outstanding Amount': parseFloat(m.outstanding_amount) || 0
                                        }))}
                                        dataKey="Paid Amount"
                                        xAxisKey="month"
                                        color="#ef4444"
                                        height={250}
                                        bars={[
                                            { dataKey: 'Paid Amount', color: '#10b981', name: t('Paid') },
                                            { dataKey: 'Outstanding Amount', color: '#f59e0b', name: t('Outstanding') },
                                        ]}
                                        showLegend
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t('No purchase data')}</div>
                                )}
                            </CardContent>
                        </Card>
                        <Card>
                            <CardHeader className="pb-2"><CardTitle className="text-md">{t('Purchase Status')}</CardTitle></CardHeader>
                            <CardContent className="h-72">
                                {purchase_vendor_analytics.status_distribution && purchase_vendor_analytics.status_distribution.length > 0 ? (
                                    <PieChart
                                        data={purchase_vendor_analytics.status_distribution.map(s => ({
                                            ...s, name: s.status, color: getPurchaseStatusColor(s.status)
                                        }))}
                                        dataKey="invoice_count" nameKey="name" donut showLegend showTooltip height={250}
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
                    </div>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap pb-2">
                            <CardTitle className="text-md">{t('Purchase Invoices')}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Select value={purchasePagePerPage} onValueChange={handlePurchasePerPageChange}>
                                    <SelectTrigger className="h-9 gap-1.5 text-sm w-32">
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
                                    data={getSortedPurchaseInvoices()}
                                    columns={[
                                        { key: 'invoice_number', header: t('Invoice #') },
                                        { key: 'invoice_date', header: t('Date') },
                                        { key: 'due_date', header: t('Due Date') },
                                        { key: 'vendor_name', header: t('Vendor') },
                                        { key: 'vendor_code', header: t('Vendor Code') },
                                        { key: 'warehouse_name', header: t('Warehouse') },
                                        { key: 'subtotal', header: t('Subtotal'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'tax_amount', header: t('Tax'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'discount_amount', header: t('Discount'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'total_amount', header: t('Total'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'paid_amount', header: t('Paid'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'balance_amount', header: t('Balance'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'status', header: t('Status') },
                                    ]}
                                    filename="purchase-invoices"
                                    title={t('Purchase Invoices')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {purchase_vendor_analytics.purchase_invoices.data && purchase_vendor_analytics.purchase_invoices.data.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchaseSort('invoice_number')}>{t('Invoice #')} {getPurchaseSortIcon('invoice_number')}</TableHead>
                                                    <TableHead>{t('Vendor')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchaseSort('invoice_date')}>{t('Date')} {getPurchaseSortIcon('invoice_date')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchaseSort('due_date')}>{t('Due')} {getPurchaseSortIcon('due_date')}</TableHead>
                                                    <TableHead>{t('Warehouse')}</TableHead>
                                                    <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchaseSort('subtotal')}>{t('Subtotal')} {getPurchaseSortIcon('subtotal')}</TableHead>
                                                    <TableHead className="text-right">{t('Tax')}</TableHead>
                                                    <TableHead className="text-right">{t('Discount')}</TableHead>
                                                    <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchaseSort('total_amount')}>{t('Total')} {getPurchaseSortIcon('total_amount')}</TableHead>
                                                    <TableHead className="text-right">{t('Paid')}</TableHead>
                                                    <TableHead className="text-right">{t('Balance')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchaseSort('status')}>{t('Status')} {getPurchaseSortIcon('status')}</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {getSortedPurchaseInvoices().map((inv: any) => (
                                                    <TableRow key={inv.id} className="hover:bg-muted/50">
                                                        {/* <TableCell className="font-medium">{inv.invoice_number}</TableCell> */}
                                                        <TableCell className="text-sm">
                                                            {pageProps?.auth?.user?.permissions?.includes("view-purchase-invoices") && inv.id ? (
                                                                <Link href={route("purchase-invoices.show", inv.id)} className="text-blue-600 hover:text-blue-700 font-medium">
                                                                    {inv.invoice_number}
                                                                </Link>
                                                            ) : (
                                                                inv.invoice_number || "-"
                                                            )}
                                                        </TableCell>
                                                        <TableCell>
                                                            <div className="flex flex-col">
                                                                <span className="font-medium">{inv.vendor_name}</span>
                                                                <span className="text-xs text-muted-foreground">
                                                                    {inv.vendor_code}
                                                                </span>
                                                            </div>
                                                        </TableCell>
                                                        <TableCell className="text-muted-foreground">{formatDate(inv.invoice_date)}</TableCell>
                                                        <TableCell className="text-muted-foreground">{formatDate(inv.due_date)}</TableCell>
                                                        <TableCell className="text-xs">{inv.warehouse_name}</TableCell>
                                                        <TableCell className="text-right">{formatCurrency(inv.subtotal || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right">{formatCurrency(inv.tax_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right">{formatCurrency(inv.discount_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right font-medium">{formatCurrency(inv.total_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right">{formatCurrency(inv.paid_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right">{formatCurrency(inv.balance_amount || 0, pageProps)}</TableCell>
                                                        <TableCell>
                                                            <span className="text-xs px-2 py-0.5 rounded-full border capitalize"
                                                                style={{ backgroundColor: getPurchaseStatusColor(inv.status) + '20', color: getPurchaseStatusColor(inv.status), borderColor: getPurchaseStatusColor(inv.status) }}>
                                                                {inv.status}
                                                            </span>
                                                        </TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t('Showing')} {purchaseFrom} {t('to')} {purchaseTo} {t('of')} {purchase_vendor_analytics.purchase_invoices.total} {t('results')}
                                        </span>
                                        {purchaseTotalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button variant="outline" size="sm" onClick={() => setPurchasePage(purchasePage - 1)} disabled={purchasePage === 1} className="h-8 text-sm">
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t('Previous')}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getPurchasePageNumbers().map((p, i) =>
                                                        typeof p === 'number' ? (
                                                            <Button key={i} variant={purchasePage === p ? 'default' : 'outline'} size="sm" onClick={() => setPurchasePage(p)} className="h-8 w-8 p-0 text-xs">
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button variant="outline" size="sm" onClick={() => setPurchasePage(purchasePage + 1)} disabled={purchasePage === purchaseTotalPages} className="h-8 text-xs">
                                                    {t('Next')}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No invoice data')}</div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap pb-2">
                            <CardTitle className="text-md">{t('Vendor Purchase Summary')}</CardTitle>
                            <ExportButton
                                data={getSortedVendorSummary()}
                                columns={[
                                    { key: 'vendor_name', header: t('Vendor') },
                                    { key: 'vendor_code', header: t('Vendor Code') },
                                    { key: 'contact_person_name', header: t('Contact Person') },
                                    { key: 'contact_person_mobile', header: t('Mobile') },
                                    { key: 'vendor_email', header: t('Email') },
                                    { key: 'total_invoices', header: t('Total Invoices') },
                                    { key: 'total_purchased', header: t('Purchased'), render: (v: number) => formatCurrency(v, pageProps) },
                                    { key: 'total_paid', header: t('Paid'), render: (v: number) => formatCurrency(v, pageProps) },
                                    { key: 'total_outstanding', header: t('Outstanding'), render: (v: number) => formatCurrency(v, pageProps) },
                                    { key: 'pending_count', header: t('Pending Bills') },
                                    { key: 'last_purchase_date', header: t('Last Purchase') },
                                    { key: 'avg_invoice_value', header: t('Avg Invoice'), render: (v: number) => formatCurrency(v, pageProps) },
                                ]}
                                filename="vendor-purchase-summary"
                                title={t('Vendor Purchase Summary')}
                            />
                        </CardHeader>
                        <CardContent className="p-0">
                            {purchase_vendor_analytics.vendor_summary && purchase_vendor_analytics.vendor_summary.length > 0 ? (
                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>{t('Vendor')}</TableHead>
                                                <TableHead>{t('Vendor Code')}</TableHead>
                                                <TableHead className="text-right">{t('Invoices')}</TableHead>
                                                <TableHead className="text-right">{t('Purchased')}</TableHead>
                                                <TableHead className="text-right">{t('Paid')}</TableHead>
                                                <TableHead className="text-right">{t('Outstanding')}</TableHead>
                                                <TableHead className="text-right">{t('Pending')}</TableHead>
                                                <TableHead>{t('Last Purchase')}</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {purchase_vendor_analytics.vendor_summary.map((ven: any, idx: number) => (
                                                <TableRow key={`vendor-${ven.vendor_code || idx}`} className="hover:bg-muted/50">
                                                    <TableCell className="font-medium">{ven.vendor_name}</TableCell>
                                                    <TableCell className="text-xs text-muted-foreground">{ven.vendor_code || '-'}</TableCell>
                                                    <TableCell className="text-right">{ven.total_invoices}</TableCell>
                                                    <TableCell className="text-right">{formatCurrency(ven.total_purchased || 0, pageProps)}</TableCell>
                                                    <TableCell className="text-right text-green-600">{formatCurrency(ven.total_paid || 0, pageProps)}</TableCell>
                                                    <TableCell className="text-right text-yellow-600">{formatCurrency(ven.total_outstanding || 0, pageProps)}</TableCell>
                                                    <TableCell className="text-right">
                                                        <span className="text-xs px-2 py-0.5">{ven.pending_count || 0}</span>
                                                    </TableCell>
                                                    <TableCell className="text-xs text-muted-foreground">{ven.last_purchase_date || '-'}</TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </div>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No vendor data')}</div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap pb-2">
                            <CardTitle className="text-md">{t('Operational Expenses')}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Select value={expensePagePerPage} onValueChange={handleExpensePerPageChange}>
                                    <SelectTrigger className="h-9 gap-1.5 text-sm w-32">
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
                                    data={getSortedExpenses()}
                                    columns={[
                                        { key: 'expense_date', header: t('Date') },
                                        { key: 'description', header: t('Description') },
                                        { key: 'category_name', header: t('Category') },
                                        { key: 'amount', header: t('Amount'), render: (v: number) => formatCurrency(v, pageProps) },
                                        { key: 'status', header: t('Status') },
                                    ]}
                                    filename="operational-expenses"
                                    title={t('Operational Expenses')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {purchase_vendor_analytics.operational_expenses.data && purchase_vendor_analytics.operational_expenses.data.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleExpenseSort('expense_date')}>{t('Date')} {getExpenseSortIcon('expense_date')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleExpenseSort('description')}>{t('Description')} {getExpenseSortIcon('description')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleExpenseSort('category_name')}>{t('Category')} {getExpenseSortIcon('category_name')}</TableHead>
                                                    <TableHead className="text-right cursor-pointer select-none hover:bg-muted/50" onClick={() => handleExpenseSort('amount')}>{t('Amount')} {getExpenseSortIcon('amount')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleExpenseSort('status')}>{t('Status')} {getExpenseSortIcon('status')}</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {getSortedExpenses().map((exp: any, idx: number) => (
                                                    <TableRow key={idx} className="hover:bg-muted/50">
                                                        <TableCell className="text-muted-foreground">{formatDate(exp.expense_date)}</TableCell>
                                                        <TableCell className="font-medium max-w-[200px]">{exp.description || '-'}</TableCell>
                                                        <TableCell>
                                                            <span className="text-xs px-2 py-0.5 rounded-full border bg-gray-100">{exp.category_name}</span>
                                                        </TableCell>
                                                        <TableCell className="text-right font-medium">{formatCurrency(exp.amount || 0, pageProps)}</TableCell>
                                                        <TableCell>
                                                            <span className={`text-xs px-2 py-0.5 rounded-full border capitalize ${exp.status === 'approved' ? 'text-green-600 bg-green-100 border-green-200' :
                                                                exp.status === 'posted' ? 'text-blue-600 bg-blue-100 border-blue-200' :
                                                                    'text-gray-600 bg-gray-100 border-gray-200'
                                                                }`}>{exp.status || '-'}</span>
                                                        </TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                        <div className="flex items-center justify-between px-4 py-3 border-t">
                                            <span className="text-sm text-muted-foreground">
                                                {t('Showing')} {expenseFrom} {t('to')} {expenseTo} {t('of')} {purchase_vendor_analytics.operational_expenses.total} {t('results')}
                                            </span>
                                            {expenseTotalPages > 1 && (
                                                <div className="flex items-center space-x-2">
                                                    <Button variant="outline" size="sm" onClick={() => setExpensePage(expensePage - 1)} disabled={expensePage === 1} className="h-8 text-sm">
                                                        <ChevronLeft className="h-4 w-4" />
                                                        {t('Previous')}
                                                    </Button>
                                                    <div className="flex items-center space-x-1">
                                                        {getExpensePageNumbers().map((p, i) =>
                                                            typeof p === 'number' ? (
                                                                <Button key={i} variant={expensePage === p ? 'default' : 'outline'} size="sm" onClick={() => setExpensePage(p)} className="h-8 w-8 p-0 text-xs">
                                                                    {p}
                                                                </Button>
                                                            ) : (
                                                                <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                            )
                                                        )}
                                                    </div>
                                                    <Button variant="outline" size="sm" onClick={() => setExpensePage(expensePage + 1)} disabled={expensePage === expenseTotalPages} className="h-8 text-xs">
                                                        {t('Next')}
                                                        <ChevronRight className="h-4 w-4" />
                                                    </Button>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No expense data')}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </AuthenticatedLayout>
    );
}
