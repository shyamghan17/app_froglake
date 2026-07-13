import { Head, usePage, Link } from '@inertiajs/react';
import React, { useState } from 'react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { LineChart } from '@/components/charts/LineChart';
import { BarChart } from '@/components/charts/BarChart';
import { PieChart } from '@/components/charts/PieChart';
import { formatCurrency, formatDate } from '@/utils/helpers';
import ExportButton from '../../Components/ExportButton';
import { Target, Briefcase, Receipt, FileText, ShoppingCart, Users, BarChart3, ChevronDown, ChevronUp, ChevronLeft, ChevronRight, TrendingUp, DollarSign, Percent, AlertCircle, CheckCircle, TrendingDown, Clock } from 'lucide-react';

interface SalesAnalyticsProps extends Record<string, any> {
    crm_pipeline: {
        kpi: { active_leads: number; active_deals: number; pipeline_value: number; conversion_rate: number };
        lead_funnel: Array<{ stage_name: string; lead_count: number; stage_pct: number; order: number }>;
        leads_by_source: Array<{ source_name: string; total_leads: number; converted: number; conversion_rate: number }>;
        monthly_leads: Array<{ month: string; total_leads: number; converted_leads: number }>;
        all_leads: { data: Array<any> };
    };
    deal_pipeline: {
        kpi: { total_deals: number; pipeline_value: number; win_rate: number; avg_deal_size: number };
        deals_by_stage: Array<{ stage_name: string; deal_count: number; total_value: number }>;
        deals_by_pipeline: Array<{ pipeline_name: string; deal_count: number; total_value: number }>;
        all_deals: { data: Array<any> };
    };
    sales_invoices: {
        kpi: { total_revenue: number; outstanding: number; overdue_count: number; proposal_conversion_rate: number };
        monthly_revenue: Array<{ month: string; invoice_count: number; subtotal: number; tax_revenue: number; total_discounts: number; gross_revenue: number; collected: number; outstanding: number }>;
        status_distribution: Array<{ status: string; count: number; total_value: number }>;
        top_products: Array<{ product_name: string; total_units_sold: number; total_revenue: number; avg_selling_price: number; total_discounts: number; total_tax: number }>;
        invoices: { data: Array<any> };
    };
    sales_proposals: {
        kpi: { total_sent: number; accepted: number; converted: number; win_rate: number };
        funnel: Array<{ status: string; count: number; total_value: number; converted_count: number }>;
        proposals: { data: Array<any> };
    };
    purchase_invoices: {
        kpi: { total_purchases: number; outstanding_payables: number; overdue_payables: number };
        monthly_purchase: Array<{ month: string; total_purchase: number; total_paid: number; total_outstanding: number; debit_notes_applied: number; invoice_count: number }>;
        top_products: Array<{ product_name: string; total_units_purchased: number; total_cost: number; avg_purchase_price: number; total_discounts: number; total_tax: number }>;
        invoices: { data: Array<any> };
    };
    customer_analytics: {
        kpi: { total_customers: number; new_customers_month: number; avg_clv: number; at_risk_customers: number };
        segmentation: Array<{ segment: string; customer_count: number; segment_revenue: number }>;
        top_customers: Array<{ customer_id: number; customer_name: string; total_invoices: number; total_revenue: number; total_paid: number; outstanding: number; last_purchase_date: string; days_since_last_purchase: number }>;
        customer_revenue: { data: Array<any> };
    };
    sales_vs_purchase: {
        kpi: { gross_profit: number; gross_margin: number; revenue_to_cost_ratio: number; net_returns_impact: number };
        monthly_comparison: Array<{ month: string; sales_revenue: number; purchase_cost: number; gross_profit: number; gross_margin: number }>;
    };
}

const statusColors: Record<string, string> = {
    paid: '#10b981',
    posted: '#3b82f6',
    partial: '#f59e0b',
    draft: '#6b7280',
    overdue: '#ef4444',
};

// Compact KPI card – smaller footprint than the old 4-line card so the
// stat row takes noticeably less vertical space on every tab.
function Kpi({ label, value, tone = 'default', icon: Icon }: { label: string; value: React.ReactNode; tone?: 'default' | 'green' | 'blue' | 'yellow' | 'red' | 'orange' | 'purple'; icon?: any }) {
    const toneClass: Record<string, string> = {
        default: 'text-foreground',
        green: 'text-green-600',
        blue: 'text-blue-600',
        yellow: 'text-yellow-600',
        red: 'text-red-600',
        orange: 'text-orange-600',
        purple: 'text-purple-600',
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

export default function SalesAnalytics() {
    const { t } = useTranslation();
    const props = usePage<SalesAnalyticsProps>().props;
    const pageProps = props as any;
    const {
        crm_pipeline, deal_pipeline, sales_invoices, sales_proposals,
        purchase_invoices, customer_analytics, sales_vs_purchase
    } = props;

    // Leads by Source state
    const [leadsBySourceData, setLeadsBySourceData] = useState(crm_pipeline.leads_by_source || []);
    const [sourcesSortField, setSourcesSortField] = useState('total_leads');
    const [sourcesSortDir, setSourcesSortDir] = useState<'asc' | 'desc'>('desc');
    const [sourcesPage, setSourcesPage] = useState(1);
    const [sourcesPerPage, setSourcesPerPage] = useState('10');

    // All Deals state
    const [allDealsData, setAllDealsData] = useState(deal_pipeline.all_deals?.data || []);
    const [dealsSortField, setDealsSortField] = useState('created_at');
    const [dealsSortDir, setDealsSortDir] = useState<'asc' | 'desc'>('desc');
    const [dealsPage, setDealsPage] = useState(1);
    const [dealsPerPage, setDealsPerPage] = useState('10');
    const [expandedDealId, setExpandedDealId] = useState<number | null>(null);
    const [showProposalFilters, setShowProposalFilters] = useState(false);
    const [proposalFilters, setProposalFilters] = useState({ date_from: '', date_to: '', status: 'all', customer: '' });

    // All Proposals state
    const [allProposalsData, setAllProposalsData] = useState(sales_proposals.proposals?.data || []);
    const [proposalsSortField, setProposalsSortField] = useState('proposal_number');
    const [proposalsSortDir, setProposalsSortDir] = useState<'asc' | 'desc'>('desc');
    const [proposalsPage, setProposalsPage] = useState(1);
    const [proposalsPerPage, setProposalsPerPage] = useState('10');

    // Purchase Invoices state
    const [allPurchaseInvoicesData, setAllPurchaseInvoicesData] = useState(purchase_invoices.invoices?.data || []);
    const [purchasesSortField, setPurchasesSortField] = useState('invoice_date');
    const [purchasesSortDir, setPurchasesSortDir] = useState<'asc' | 'desc'>('desc');
    const [purchasesPage, setPurchasesPage] = useState(1);
    const [purchasesPerPage, setPurchasesPerPage] = useState('10');
    const [purchaseStatusFilter, setPurchaseStatusFilter] = useState<string>('all');
    const [expandedPurchaseId, setExpandedPurchaseId] = useState<number | null>(null);
    const [showPurchasesFilters, setShowPurchasesFilters] = useState(false);
    const [purchaseFilters, setPurchaseFilters] = useState({ date_from: '', date_to: '', vendor: '', status: 'all' });

    // Sales Invoices state
    const [allSalesInvoicesData, setAllSalesInvoicesData] = useState(sales_invoices.invoices?.data || []);
    const [salesInvoicesSortField, setSalesInvoicesSortField] = useState('invoice_date');
    const [salesInvoicesSortDir, setSalesInvoicesSortDir] = useState<'asc' | 'desc'>('desc');
    const [salesInvoicesPage, setSalesInvoicesPage] = useState(1);
    const [salesInvoicesPerPage, setSalesInvoicesPerPage] = useState('10');
    const [salesStatusFilter, setSalesStatusFilter] = useState<string>('all');
    const [expandedSalesInvoiceId, setExpandedSalesInvoiceId] = useState<number | null>(null);
    const [showSalesFilters, setShowSalesFilters] = useState(false);
    const [salesFilters, setSalesFilters] = useState({ date_from: '', date_to: '', customer: '', status: 'all' });

    // Customer Analytics state
    const [allCustomersData, setAllCustomersData] = useState(customer_analytics.customer_revenue?.data || []);
    const [customersSortField, setCustomersSortField] = useState('total_revenue');
    const [customersSortDir, setCustomersSortDir] = useState<'asc' | 'desc'>('desc');
    const [customersPage, setCustomersPage] = useState(1);
    const [customersPerPage, setCustomersPerPage] = useState('10');
    const [segmentFilter, setSegmentFilter] = useState<string>('all');
    const [riskFilter, setRiskFilter] = useState<string>('all');
    const [expandedCustomerId, setExpandedCustomerId] = useState<number | null>(null);
    const [showCustomerFilters, setShowCustomerFilters] = useState(false);
    const [customerFilters, setCustomerFilters] = useState({ search: '', segment: 'all', risk: 'all', min_revenue: '' });

    const handleSourcesSort = (field: string) => {
        const newDir: 'asc' | 'desc' = sourcesSortField === field && sourcesSortDir === 'desc' ? 'asc' : 'desc';
        setSourcesSortField(field);
        setSourcesSortDir(newDir);
        setSourcesPage(1);

        const sorted = [...leadsBySourceData].sort((a, b) => {
            const aVal = a[field as keyof typeof a] ?? '';
            const bVal = b[field as keyof typeof b] ?? '';
            if (typeof aVal === 'number' && typeof bVal === 'number') {
                return newDir === 'asc' ? aVal - bVal : bVal - aVal;
            }
            return newDir === 'asc' ? String(aVal).localeCompare(String(bVal)) : String(bVal).localeCompare(String(aVal));
        });
        setLeadsBySourceData(sorted);
    };

    const getSourcesSortIcon = (field: string) => {
        if (sourcesSortField !== field) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return sourcesSortDir === 'asc' ? (
            <ChevronUp className="h-4 w-4 inline ml-1" />
        ) : (
            <ChevronDown className="h-4 w-4 inline ml-1" />
        );
    };

    const sourcesPerPageNum = parseInt(sourcesPerPage, 10);
    const sourcesTotalPages = Math.ceil(leadsBySourceData.length / sourcesPerPageNum);
    const sourcesPaginatedData = leadsBySourceData.slice(
        (sourcesPage - 1) * sourcesPerPageNum,
        sourcesPage * sourcesPerPageNum
    );
    const sourcesFrom = leadsBySourceData.length > 0 ? (sourcesPage - 1) * sourcesPerPageNum + 1 : 0;
    const sourcesTo = Math.min(sourcesPage * sourcesPerPageNum, leadsBySourceData.length);

    const getSourcesPageNumbers = () => {
        const p: (number | string)[] = [];
        if (sourcesTotalPages <= 7) {
            for (let i = 1; i <= sourcesTotalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (sourcesPage > 3) p.push('...');
            const s = Math.max(2, sourcesPage - 1);
            const e = Math.min(sourcesTotalPages - 1, sourcesPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (sourcesPage < sourcesTotalPages - 2) p.push('...');
            p.push(sourcesTotalPages);
        }
        return p;
    };

    const handleSalesInvoicesSort = (field: string) => {
        const newDir: 'asc' | 'desc' = salesInvoicesSortField === field && salesInvoicesSortDir === 'desc' ? 'asc' : 'desc';
        setSalesInvoicesSortField(field);
        setSalesInvoicesSortDir(newDir);
        setSalesInvoicesPage(1);
        const sorted = [...allSalesInvoicesData].sort((a, b) => {
            const aVal = a[field as keyof typeof a] ?? '';
            const bVal = b[field as keyof typeof b] ?? '';
            if (typeof aVal === 'number' && typeof bVal === 'number') {
                return newDir === 'asc' ? aVal - bVal : bVal - aVal;
            }
            return newDir === 'asc' ? String(aVal).localeCompare(String(bVal)) : String(bVal).localeCompare(String(aVal));
        });
        setAllSalesInvoicesData(sorted);
    };

    const getSalesInvoicesSortIcon = (field: string) => {
        if (salesInvoicesSortField !== field) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return salesInvoicesSortDir === 'asc' ? <ChevronUp className="h-4 w-4 inline ml-1" /> : <ChevronDown className="h-4 w-4 inline ml-1" />;
    };

    const filteredSalesInvoicesData = allSalesInvoicesData.filter((inv: any) => {
        return salesStatusFilter === 'all' || inv.status === salesStatusFilter;
    });

    const salesInvoicesPerPageNum = parseInt(salesInvoicesPerPage, 10);
    const salesInvoicesTotalPages = Math.ceil(filteredSalesInvoicesData.length / salesInvoicesPerPageNum);
    const salesInvoicesPaginatedData = filteredSalesInvoicesData.slice((salesInvoicesPage - 1) * salesInvoicesPerPageNum, salesInvoicesPage * salesInvoicesPerPageNum);
    const salesInvoicesFrom = filteredSalesInvoicesData.length > 0 ? (salesInvoicesPage - 1) * salesInvoicesPerPageNum + 1 : 0;
    const salesInvoicesTo = Math.min(salesInvoicesPage * salesInvoicesPerPageNum, filteredSalesInvoicesData.length);

    const getSalesInvoicesPageNumbers = () => {
        const p: (number | string)[] = [];
        if (salesInvoicesTotalPages <= 7) {
            for (let i = 1; i <= salesInvoicesTotalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (salesInvoicesPage > 3) p.push('...');
            const s = Math.max(2, salesInvoicesPage - 1);
            const e = Math.min(salesInvoicesTotalPages - 1, salesInvoicesPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (salesInvoicesPage < salesInvoicesTotalPages - 2) p.push('...');
            p.push(salesInvoicesTotalPages);
        }
        return p;
    };

    const handleDealsSort = (field: string) => {
        const newDir: 'asc' | 'desc' = dealsSortField === field && dealsSortDir === 'desc' ? 'asc' : 'desc';
        setDealsSortField(field);
        setDealsSortDir(newDir);
        setDealsPage(1);
        const sorted = [...allDealsData].sort((a, b) => {
            const aVal = a[field as keyof typeof a] ?? '';
            const bVal = b[field as keyof typeof b] ?? '';
            if (typeof aVal === 'number' && typeof bVal === 'number') {
                return newDir === 'asc' ? aVal - bVal : bVal - aVal;
            }
            return newDir === 'asc' ? String(aVal).localeCompare(String(bVal)) : String(bVal).localeCompare(String(aVal));
        });
        setAllDealsData(sorted);
    };

    const getDealsSortIcon = (field: string) => {
        if (dealsSortField !== field) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return dealsSortDir === 'asc' ? <ChevronUp className="h-4 w-4 inline ml-1" /> : <ChevronDown className="h-4 w-4 inline ml-1" />;
    };

    const dealsPerPageNum = parseInt(dealsPerPage, 10);
    const dealsTotalPages = Math.ceil(allDealsData.length / dealsPerPageNum);
    const dealsPaginatedData = allDealsData.slice((dealsPage - 1) * dealsPerPageNum, dealsPage * dealsPerPageNum);
    const dealsFrom = allDealsData.length > 0 ? (dealsPage - 1) * dealsPerPageNum + 1 : 0;
    const dealsTo = Math.min(dealsPage * dealsPerPageNum, allDealsData.length);

    const getDealsPageNumbers = () => {
        const p: (number | string)[] = [];
        if (dealsTotalPages <= 7) {
            for (let i = 1; i <= dealsTotalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (dealsPage > 3) p.push('...');
            const s = Math.max(2, dealsPage - 1);
            const e = Math.min(dealsTotalPages - 1, dealsPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (dealsPage < dealsTotalPages - 2) p.push('...');
            p.push(dealsTotalPages);
        }
        return p;
    };

    const getStatusBadgeStyle = (status: string) => {
        switch (status) {
            case "High Value":
            case "posted":
            case "sent":
            case "Open":
                return "bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800";
            case "Medium Value":
                return "bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-900/30 dark:text-purple-400 dark:border-purple-800";
            case "Low Value":
                return "bg-orange-100 text-orange-800 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800";
            case "Active":
            case "VIP":
            case "paid":
            case "accepted":
            case "Won":
                return "bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800";
            case "At Risk":
            case "overdue":
            case "rejected":
            case "Lost":
                return "bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800";
            case "partial":
                return "bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800";
            default:
                return "bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700";
        }
    };

    const handleProposalsSort = (field: string) => {
        const newDir: 'asc' | 'desc' = proposalsSortField === field && proposalsSortDir === 'desc' ? 'asc' : 'desc';
        setProposalsSortField(field);
        setProposalsSortDir(newDir);
        setProposalsPage(1);
        const sorted = [...allProposalsData].sort((a, b) => {
            const aVal = a[field as keyof typeof a] ?? '';
            const bVal = b[field as keyof typeof b] ?? '';
            if (typeof aVal === 'number' && typeof bVal === 'number') {
                return newDir === 'asc' ? aVal - bVal : bVal - aVal;
            }
            return newDir === 'asc' ? String(aVal).localeCompare(String(bVal)) : String(bVal).localeCompare(String(aVal));
        });
        setAllProposalsData(sorted);
    };

    const getProposalsSortIcon = (field: string) => {
        if (proposalsSortField !== field) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return proposalsSortDir === 'asc' ? <ChevronUp className="h-4 w-4 inline ml-1" /> : <ChevronDown className="h-4 w-4 inline ml-1" />;
    };

    const proposalsPerPageNum = parseInt(proposalsPerPage, 10);
    const proposalsTotalPages = Math.ceil(allProposalsData.length / proposalsPerPageNum);
    const proposalsPaginatedData = allProposalsData.slice((proposalsPage - 1) * proposalsPerPageNum, proposalsPage * proposalsPerPageNum);
    const proposalsFrom = allProposalsData.length > 0 ? (proposalsPage - 1) * proposalsPerPageNum + 1 : 0;
    const proposalsTo = Math.min(proposalsPage * proposalsPerPageNum, allProposalsData.length);

    const getProposalsPageNumbers = () => {
        const p: (number | string)[] = [];
        if (proposalsTotalPages <= 7) {
            for (let i = 1; i <= proposalsTotalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (proposalsPage > 3) p.push('...');
            const s = Math.max(2, proposalsPage - 1);
            const e = Math.min(proposalsTotalPages - 1, proposalsPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (proposalsPage < proposalsTotalPages - 2) p.push('...');
            p.push(proposalsTotalPages);
        }
        return p;
    };

    const handlePurchasesSort = (field: string) => {
        const newDir: 'asc' | 'desc' = purchasesSortField === field && purchasesSortDir === 'desc' ? 'asc' : 'desc';
        setPurchasesSortField(field);
        setPurchasesSortDir(newDir);
        setPurchasesPage(1);
        const sorted = [...allPurchaseInvoicesData].sort((a, b) => {
            const aVal = a[field as keyof typeof a] ?? '';
            const bVal = b[field as keyof typeof b] ?? '';
            if (typeof aVal === 'number' && typeof bVal === 'number') {
                return newDir === 'asc' ? aVal - bVal : bVal - aVal;
            }
            return newDir === 'asc' ? String(aVal).localeCompare(String(bVal)) : String(bVal).localeCompare(String(aVal));
        });
        setAllPurchaseInvoicesData(sorted);
    };

    const getPurchasesSortIcon = (field: string) => {
        if (purchasesSortField !== field) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return purchasesSortDir === 'asc' ? <ChevronUp className="h-4 w-4 inline ml-1" /> : <ChevronDown className="h-4 w-4 inline ml-1" />;
    };

    const filteredPurchasesData = allPurchaseInvoicesData.filter((inv: any) => {
        return purchaseStatusFilter === 'all' || inv.status === purchaseStatusFilter;
    });

    const purchasesPerPageNum = parseInt(purchasesPerPage, 10);
    const purchasesTotalPages = Math.ceil(filteredPurchasesData.length / purchasesPerPageNum);
    const purchasesPaginatedData = filteredPurchasesData.slice((purchasesPage - 1) * purchasesPerPageNum, purchasesPage * purchasesPerPageNum);
    const purchasesFrom = filteredPurchasesData.length > 0 ? (purchasesPage - 1) * purchasesPerPageNum + 1 : 0;
    const purchasesTo = Math.min(purchasesPage * purchasesPerPageNum, filteredPurchasesData.length);

    const getPurchasesPageNumbers = () => {
        const p: (number | string)[] = [];
        if (purchasesTotalPages <= 7) {
            for (let i = 1; i <= purchasesTotalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (purchasesPage > 3) p.push('...');
            const s = Math.max(2, purchasesPage - 1);
            const e = Math.min(purchasesTotalPages - 1, purchasesPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (purchasesPage < purchasesTotalPages - 2) p.push('...');
            p.push(purchasesTotalPages);
        }
        return p;
    };

    const handleCustomersSort = (field: string) => {
        const newDir: 'asc' | 'desc' = customersSortField === field && customersSortDir === 'desc' ? 'asc' : 'desc';
        setCustomersSortField(field);
        setCustomersSortDir(newDir);
        setCustomersPage(1);
        const sorted = [...allCustomersData].sort((a, b) => {
            const aVal = a[field as keyof typeof a] ?? '';
            const bVal = b[field as keyof typeof b] ?? '';
            if (typeof aVal === 'number' && typeof bVal === 'number') {
                return newDir === 'asc' ? aVal - bVal : bVal - aVal;
            }
            return newDir === 'asc' ? String(aVal).localeCompare(String(bVal)) : String(bVal).localeCompare(String(aVal));
        });
        setAllCustomersData(sorted);
    };

    const getCustomersSortIcon = (field: string) => {
        if (customersSortField !== field) return <ChevronDown className="h-4 w-4 inline ml-1" />;
        return customersSortDir === 'asc' ? <ChevronUp className="h-4 w-4 inline ml-1" /> : <ChevronDown className="h-4 w-4 inline ml-1" />;
    };

    const filteredCustomersData = allCustomersData.filter((c: any) => {
        const segmentMatch = segmentFilter === 'all' || c.segment === segmentFilter;
        const riskMatch = riskFilter === 'all' || c.risk_status === riskFilter;
        return segmentMatch && riskMatch;
    });

    const customersPerPageNum = parseInt(customersPerPage, 10);
    const customersTotalPages = Math.ceil(filteredCustomersData.length / customersPerPageNum);
    const customersPaginatedData = filteredCustomersData.slice((customersPage - 1) * customersPerPageNum, customersPage * customersPerPageNum);
    const customersFrom = filteredCustomersData.length > 0 ? (customersPage - 1) * customersPerPageNum + 1 : 0;
    const customersTo = Math.min(customersPage * customersPerPageNum, filteredCustomersData.length);

    const getCustomersPageNumbers = () => {
        const p: (number | string)[] = [];
        if (customersTotalPages <= 7) {
            for (let i = 1; i <= customersTotalPages; i++) p.push(i);
        } else {
            p.push(1);
            if (customersPage > 3) p.push('...');
            const s = Math.max(2, customersPage - 1);
            const e = Math.min(customersTotalPages - 1, customersPage + 1);
            for (let i = s; i <= e; i++) p.push(i);
            if (customersPage < customersTotalPages - 2) p.push('...');
            p.push(customersTotalPages);
        }
        return p;
    };

    // Tab definitions used to render the nav strip — keeping this as data
    // means adding/removing a section never requires touching markup twice.
    const tabs = [
        { value: 'leads', label: t('CRM & Leads'), icon: Target },
        { value: 'deals', label: t('Deals'), icon: Briefcase },
        { value: 'invoices', label: t('Sales Invoices'), icon: Receipt },
        { value: 'proposals', label: t('Proposals'), icon: FileText },
        { value: 'purchases', label: t('Purchases'), icon: ShoppingCart },
        { value: 'customers', label: t('Customers'), icon: Users },
        { value: 'comparison', label: t('Sales vs Purchase'), icon: BarChart3 },
    ];

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Smart Dashboard'), url: route('smart-analytics.dashboard') },
                { label: t('Sales & Customer') }
            ]}
            pageTitle={t('Sales & Customer')}
        >
            <Head title={t('Sales & Customer')} />

            <Tabs defaultValue="leads" className="w-full">
                {/* Sticky tab strip: one section is visible at a time, so the
                   page that used to be ~7 screens tall is now a single
                   screen's worth of content per tab. */}
                <TabsList className="sticky top-0 z-10 mb-6 h-auto w-full flex-wrap justify-start gap-1 overflow-x-auto bg-muted/60 p-1.5 backdrop-blur supports-[backdrop-filter]:bg-muted/50">
                    {tabs.map(({ value, label, icon: Icon }) => (
                        <TabsTrigger key={value} value={value} className="gap-1.5 whitespace-nowrap text-xs sm:text-sm">
                            <Icon className="h-3.5 w-3.5" />
                            {label}
                        </TabsTrigger>
                    ))}
                </TabsList>

                {/* ==================== TAB 1: CRM PIPELINE & LEAD ANALYTICS ==================== */}
                <TabsContent value="leads" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                        <Kpi label={t('Active Leads')} value={crm_pipeline.kpi.active_leads} icon={Target} tone="blue" />
                        <Kpi label={t('Pipeline Value')} value={`${formatCurrency(crm_pipeline.kpi.pipeline_value || 0, pageProps)}`} icon={DollarSign} tone="green" />
                        <Kpi label={t('Conversion Rate')} value={`${crm_pipeline.kpi.conversion_rate}%`} icon={Percent} tone="orange" />
                        <Kpi label={t('Active Deals')} value={crm_pipeline.kpi.active_deals} icon={Briefcase} tone="purple" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t('Lead Funnel by Stage')}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {crm_pipeline.lead_funnel?.length > 0 ? (
                                    <BarChart
                                        data={crm_pipeline.lead_funnel.map((l: any) => ({
                                            stage_name: l.stage_name,
                                            'Lead Count': parseInt(l.lead_count) || 0,
                                            stage_pct: l.stage_pct,
                                            order: l.order
                                        }))}
                                        dataKey="Lead Count"
                                        xAxisKey="stage_name"
                                        color="#3b82f6"
                                        height={250}
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">
                                        {t('No lead data available')}
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t('Monthly Lead Volume')}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {crm_pipeline.monthly_leads?.length > 0 ? (
                                    <LineChart
                                        data={crm_pipeline.monthly_leads}
                                        xAxisKey="month"
                                        color="#3b82f6"
                                        height={250}
                                        lines={[
                                            { dataKey: 'total_leads', color: '#3b82f6', name: t('Total Leads') },
                                            { dataKey: 'converted_leads', color: '#10b981', name: t('Converted') },
                                        ]}
                                        showLegend
                                        showDots
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">
                                        {t('No lead volume data')}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2">
                            <CardTitle className="text-md">{t('Leads by Source')}</CardTitle>
                            <div className="flex items-center gap-2">
                                <Select
                                    value={sourcesPerPage}
                                    onValueChange={(v: string) => {
                                        setSourcesPerPage(v);
                                        setSourcesPage(1);
                                    }}
                                >
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
                                    data={leadsBySourceData}
                                    columns={[
                                        { key: 'source_name', header: t('Source') },
                                        { key: 'total_leads', header: t('Total Leads') },
                                        { key: 'converted', header: t('Converted') },
                                        { key: 'conversion_rate', header: t('Conversion Rate %'), render: (v: number) => `${v}%` },
                                    ]}
                                    filename="leads-by-source"
                                    title={t('Leads by Source')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {leadsBySourceData.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead
                                                        className="cursor-pointer select-none hover:bg-muted/50"
                                                        onClick={() => handleSourcesSort('source_name')}
                                                    >
                                                        {t('Source')}
                                                        {getSourcesSortIcon('source_name')}
                                                    </TableHead>
                                                    <TableHead
                                                        className="cursor-pointer select-none hover:bg-muted/50 text-right"
                                                        onClick={() => handleSourcesSort('total_leads')}
                                                    >
                                                        {t('Total Leads')}
                                                        {getSourcesSortIcon('total_leads')}
                                                    </TableHead>
                                                    <TableHead
                                                        className="cursor-pointer select-none hover:bg-muted/50 text-right"
                                                        onClick={() => handleSourcesSort('converted')}
                                                    >
                                                        {t('Converted')}
                                                        {getSourcesSortIcon('converted')}
                                                    </TableHead>
                                                    <TableHead
                                                        className="cursor-pointer select-none hover:bg-muted/50 text-right"
                                                        onClick={() => handleSourcesSort('conversion_rate')}
                                                    >
                                                        {t('Conversion Rate')}
                                                        {getSourcesSortIcon('conversion_rate')}
                                                    </TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {sourcesPaginatedData.map((src: any, idx: number) => (
                                                    <TableRow key={idx} className="hover:bg-muted/50">
                                                        <TableCell className="font-medium">{src.source_name}</TableCell>
                                                        <TableCell className="text-right">{src.total_leads}</TableCell>
                                                        <TableCell className="text-right text-green-600 font-medium">{src.converted}</TableCell>
                                                        <TableCell className="text-right font-medium">{src.conversion_rate}%</TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t('Showing')} {sourcesFrom} {t('to')} {sourcesTo} {t('of')}{' '}
                                            {leadsBySourceData.length} {t('results')}
                                        </span>
                                        {sourcesTotalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setSourcesPage(sourcesPage - 1)}
                                                    disabled={sourcesPage === 1}
                                                    className="h-8 text-sm"
                                                >
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t('Previous')}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getSourcesPageNumbers().map((p, i) =>
                                                        typeof p === 'number' ? (
                                                            <Button
                                                                key={i}
                                                                variant={sourcesPage === p ? 'default' : 'outline'}
                                                                size="sm"
                                                                onClick={() => setSourcesPage(p)}
                                                                className="h-8 w-8 p-0 text-xs"
                                                            >
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">
                                                                ...
                                                            </span>
                                                        )
                                                    )}
                                                </div>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setSourcesPage(sourcesPage + 1)}
                                                    disabled={sourcesPage === sourcesTotalPages}
                                                    className="h-8 text-xs"
                                                >
                                                    {t('Next')}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No source data')}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 2: DEAL PIPELINE & OPPORTUNITY ==================== */}
                <TabsContent value="deals" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                        <Kpi label={t('Total Active Deals')} value={deal_pipeline.kpi.total_deals} icon={Briefcase} tone="blue" />
                        <Kpi label={t('Total Pipeline Value')} value={`${formatCurrency(deal_pipeline.kpi.pipeline_value || 0, pageProps)}`} icon={DollarSign} tone="green" />
                        <Kpi label={t('Win Rate')} value={`${deal_pipeline.kpi.win_rate}%`} icon={TrendingUp} tone="green" />
                        <Kpi label={t('Avg Deal Size')} value={`${formatCurrency(deal_pipeline.kpi.avg_deal_size || 0, pageProps)}`} icon={Receipt} tone="orange" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t('Deals by Stage')}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {deal_pipeline.deals_by_stage?.length > 0 ? (
                                    <BarChart
                                        data={deal_pipeline.deals_by_stage.map((d: any) => ({
                                            stage_name: d.stage_name,
                                            'Deal Count': parseInt(d.deal_count) || 0,
                                            'Total Value': parseFloat(d.total_value) || 0
                                        }))}
                                        dataKey="Deal Count"
                                        xAxisKey="stage_name"
                                        color="#8b5cf6"
                                        height={250}
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">
                                        {t('No deal data')}
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t('Deals by Pipeline')}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {deal_pipeline.deals_by_pipeline?.length > 0 ? (
                                    <PieChart
                                        data={deal_pipeline.deals_by_pipeline.map((p: any) => ({
                                            ...p,
                                            name: p.pipeline_name,
                                            value: p.deal_count,
                                            color: ['#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#3b82f6'][deal_pipeline.deals_by_pipeline.indexOf(p) % 5]
                                        }))}
                                        dataKey="value"
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
                                            height={250}
                                        />
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2">
                            <CardTitle className="text-md">{t('All Deals')}</CardTitle>
                            <div className="flex items-center gap-2">
                                <Select
                                    value={dealsPerPage}
                                    onValueChange={(v: string) => {
                                        setDealsPerPage(v);
                                        setDealsPage(1);
                                    }}
                                >
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
                                    data={allDealsData}
                                    columns={[
                                        { key: 'deal_name', header: t('Deal Name') },
                                        { key: 'client_names', header: t('Client(s)') },
                                        { key: 'pipeline_name', header: t('Pipeline') },
                                        { key: 'stage_name', header: t('Stage') },
                                        { key: 'deal_value', header: t('Value'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'task_count', header: t('Tasks') },
                                        { key: 'call_count', header: t('Calls') },
                                        { key: 'email_count', header: t('Emails') },
                                        { key: 'status', header: t('Status') },
                                    ]}
                                    filename="all-deals"
                                    title={t('All Deals')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {allDealsData.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleDealsSort('deal_name')}>
                                                        {t('Deal Name')} {getDealsSortIcon('deal_name')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleDealsSort('client_names')}>
                                                        {t('Client(s)')} {getDealsSortIcon('client_names')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleDealsSort('pipeline_name')}>
                                                        {t('Pipeline')} {getDealsSortIcon('pipeline_name')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleDealsSort('stage_name')}>
                                                        {t('Stage')} {getDealsSortIcon('stage_name')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50 text-right" onClick={() => handleDealsSort('deal_value')}>
                                                        {t('Value')} {getDealsSortIcon('deal_value')}
                                                    </TableHead>
                                                    <TableHead className="text-right">{t('Tasks')}</TableHead>
                                                    <TableHead className="text-right">{t('Calls')}</TableHead>
                                                    <TableHead className="text-right">{t('Emails')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleDealsSort('status')}>
                                                        {t('Status')} {getDealsSortIcon('status')}
                                                    </TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {dealsPaginatedData.map((deal: any) => (
                                                    <TableRow key={deal.id} className="hover:bg-muted/50" onClick={() => setExpandedDealId(expandedDealId === deal.id ? null : deal.id)}>
                                                        <TableCell className="font-medium">{deal.deal_name}</TableCell>
                                                        <TableCell className="text-sm">{deal.client_names || '-'}</TableCell>
                                                        <TableCell className="text-sm">{deal.pipeline_name}</TableCell>
                                                        <TableCell className="text-sm">{deal.stage_name}</TableCell>
                                                        <TableCell className="text-right font-medium">{formatCurrency(deal.deal_value || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm">{deal.task_count}</TableCell>
                                                        <TableCell className="text-right text-sm">{deal.call_count}</TableCell>
                                                        <TableCell className="text-right text-sm">{deal.email_count}</TableCell>
                                                        <TableCell>
                                                            <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize ${getStatusBadgeStyle(deal.status)}`}>{t(deal.status)}</span>
                                                        </TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t('Showing')} {dealsFrom} {t('to')} {dealsTo} {t('of')} {allDealsData.length} {t('results')}
                                        </span>
                                        {dealsTotalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setDealsPage(dealsPage - 1)}
                                                    disabled={dealsPage === 1}
                                                    className="h-8 text-sm"
                                                >
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t('Previous')}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getDealsPageNumbers().map((p, i) =>
                                                        typeof p === 'number' ? (
                                                            <Button
                                                                key={i}
                                                                variant={dealsPage === p ? 'default' : 'outline'}
                                                                size="sm"
                                                                onClick={() => setDealsPage(p)}
                                                                className="h-8 w-8 p-0 text-xs"
                                                            >
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setDealsPage(dealsPage + 1)}
                                                    disabled={dealsPage === dealsTotalPages}
                                                    className="h-8 text-xs"
                                                >
                                                    {t('Next')}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No deals data')}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 3: SALES INVOICES ANALYTICS ==================== */}
                <TabsContent value="invoices" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                        <Kpi label={t('Total Revenue')} value={`${formatCurrency(sales_invoices.kpi.total_revenue || 0, pageProps)}`} icon={DollarSign} tone="green" />
                        <Kpi label={t('Outstanding Amount')} value={`${formatCurrency(sales_invoices.kpi.outstanding || 0, pageProps)}`} icon={Clock} tone="yellow" />
                        <Kpi label={t('Overdue Invoice')} value={sales_invoices.kpi.overdue_count} icon={AlertCircle} tone="red" />
                        <Kpi label={t('Proposal Conversion Rate')} value={`${sales_invoices.kpi.proposal_conversion_rate}%`} icon={CheckCircle} tone="blue" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t('Monthly Revenue')}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {sales_invoices.monthly_revenue?.length > 0 ? (
                                    <BarChart
                                        data={sales_invoices.monthly_revenue.map((m: any) => ({
                                            month: m.month,
                                            'Collected': m.collected,
                                            'Outstanding': m.outstanding,
                                            'Invoice Count': m.invoice_count,
                                            'Subtotal': m.subtotal,
                                            'Tax Revenue': m.tax_revenue,
                                            'Total Discounts': m.total_discounts,
                                            'Gross Revenue': m.gross_revenue
                                        }))}
                                        xAxisKey="month"
                                        height={250}
                                        bars={[
                                            { dataKey: 'Collected', color: '#10b981', name: t('Collected') },
                                            { dataKey: 'Outstanding', color: '#f59e0b', name: t('Outstanding') },
                                        ]}
                                        showLegend
                                        showTooltip
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">
                                        {t('No revenue data')}
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t('Invoice Status Distribution')}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {sales_invoices.status_distribution?.length > 0 ? (
                                    <PieChart
                                        data={sales_invoices.status_distribution.map((s: any) => ({
                                            ...s,
                                            name: s.status,
                                            value: s.count,
                                            color: statusColors[s.status] || '#6b7280'
                                        }))}
                                        dataKey="value"
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
                                            height={250}
                                        />
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-md">{t('Top 10 Products by Sales Revenue')}</CardTitle>
                        </CardHeader>
                        <CardContent className="h-72">
                            {sales_invoices.top_products?.length > 0 ? (
                                <BarChart
                                    data={sales_invoices.top_products.map((p: any) => ({
                                        product_name: p.product_name,
                                        'Total Revenue': parseFloat(p.total_revenue) || 0,
                                        total_units_sold: p.total_units_sold,
                                        avg_selling_price: p.avg_selling_price
                                    }))}
                                    dataKey="Total Revenue"
                                    xAxisKey="product_name"
                                    color="#8b5cf6"
                                    height={250}
                                />
                            ) : (
                                <div className="flex items-center justify-center h-full text-muted-foreground text-sm">
                                    {t('No product data')}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap">
                            <CardTitle className="text-md">{t('All Sales Invoices')}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Select value={salesInvoicesPerPage} onValueChange={(v: string) => { setSalesInvoicesPerPage(v); setSalesInvoicesPage(1); }}>
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
                                    data={allSalesInvoicesData}
                                    columns={[
                                        { key: 'invoice_number', header: t('Invoice #') },
                                        { key: 'invoice_date', header: t('Date'), render: (v: string) => formatDate(v, pageProps) },
                                        { key: 'due_date', header: t('Due Date'), render: (v: string) => formatDate(v, pageProps) },
                                        { key: 'customer_name', header: t('Customer') },
                                        { key: 'type', header: t('Type') },
                                        { key: 'subtotal', header: t('Subtotal'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'tax_amount', header: t('Tax'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'discount_amount', header: t('Discount'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'total_amount', header: t('Total'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'paid_amount', header: t('Paid'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'balance_amount', header: t('Balance'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'status', header: t('Status') },
                                        { key: 'journal_number', header: t('Journal #') },
                                        { key: 'return_count', header: t('Returns') },
                                        { key: 'created_by_name', header: t('Created By') },
                                    ]}
                                    filename="sales-invoices"
                                    title={t('Sales Invoices')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {salesInvoicesPaginatedData.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleSalesInvoicesSort('invoice_number')}>
                                                        {t('Invoice #')} {getSalesInvoicesSortIcon('invoice_number')}
                                                    </TableHead>
                                                    <TableHead>{t('Journal #')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleSalesInvoicesSort('invoice_date')}>
                                                        {t('Date')} {getSalesInvoicesSortIcon('invoice_date')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleSalesInvoicesSort('due_date')}>
                                                        {t('Due Date')} {getSalesInvoicesSortIcon('due_date')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleSalesInvoicesSort('customer_name')}>
                                                        {t('Customer')} {getSalesInvoicesSortIcon('customer_name')}
                                                    </TableHead>
                                                    <TableHead>{t('Type')}</TableHead>
                                                    <TableHead className="text-right">{t('Subtotal')}</TableHead>
                                                    <TableHead className="text-right">{t('Tax')}</TableHead>
                                                    <TableHead className="text-right">{t('Discount')}</TableHead>
                                                    <TableHead className="text-right font-medium">{t('Total')}</TableHead>
                                                    <TableHead className="text-right">{t('Paid')}</TableHead>
                                                    <TableHead className="text-right">{t('Balance')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleSalesInvoicesSort('status')}>
                                                        {t('Status')} {getSalesInvoicesSortIcon('status')}
                                                    </TableHead>
                                                    <TableHead className="text-center">{t('Returns')}</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {salesInvoicesPaginatedData.map((invoice: any) => (
                                                    <TableRow key={invoice.id} className="hover:bg-muted/50" onClick={() => setExpandedSalesInvoiceId(expandedSalesInvoiceId === invoice.id ? null : invoice.id)}>
                                                        <TableCell className="sm">
                                                            {pageProps?.auth?.user?.permissions?.includes("view-sales-invoices") && invoice.id ? (
                                                                <Link href={route("sales-invoices.show", invoice.id)} className="text-blue-600 hover:text-blue-700 font-medium">
                                                                    {invoice.invoice_number}
                                                                </Link>
                                                            ) : (
                                                                invoice.invoice_number || "-"
                                                            )}
                                                        </TableCell>
                                                        <TableCell className="text-sm">{invoice.journal_number || '-'}</TableCell>
                                                        <TableCell className="text-sm">{formatDate(invoice.invoice_date, pageProps)}</TableCell>
                                                        <TableCell className="text-sm">{formatDate(invoice.due_date, pageProps)}</TableCell>
                                                        <TableCell className="text-sm">{invoice.customer_name}</TableCell>
                                                        <TableCell className="text-sm">{invoice.type}</TableCell>
                                                        <TableCell className="text-right text-sm">{formatCurrency(invoice.subtotal || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm">{formatCurrency(invoice.tax_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm">{formatCurrency(invoice.discount_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right font-medium">{formatCurrency(invoice.total_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm text-green-600">{formatCurrency(invoice.paid_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm text-yellow-600">{formatCurrency(invoice.balance_amount || 0, pageProps)}</TableCell>
                                                        <TableCell>
                                                            <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize ${getStatusBadgeStyle(invoice.status)}`}>{t(invoice.status)}</span>
                                                        </TableCell>
                                                        <TableCell className="text-center text-sm">{invoice.return_quantity || 0}</TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t('Showing')} {salesInvoicesFrom} {t('to')} {salesInvoicesTo} {t('of')} {filteredSalesInvoicesData.length} {t('results')}
                                        </span>
                                        {salesInvoicesTotalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setSalesInvoicesPage(salesInvoicesPage - 1)}
                                                    disabled={salesInvoicesPage === 1}
                                                    className="h-8 text-sm"
                                                >
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t('Previous')}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getSalesInvoicesPageNumbers().map((p, i) =>
                                                        typeof p === 'number' ? (
                                                            <Button
                                                                key={i}
                                                                variant={salesInvoicesPage === p ? 'default' : 'outline'}
                                                                size="sm"
                                                                onClick={() => setSalesInvoicesPage(p)}
                                                                className="h-8 w-8 p-0 text-xs"
                                                            >
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setSalesInvoicesPage(salesInvoicesPage + 1)}
                                                    disabled={salesInvoicesPage === salesInvoicesTotalPages}
                                                    className="h-8 text-xs"
                                                >
                                                    {t('Next')}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No sales invoices')}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 4: SALES PROPOSALS ==================== */}
                <TabsContent value="proposals" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                        <Kpi label={t('Total Sent')} value={sales_proposals.kpi.total_sent} icon={FileText} tone="blue" />
                        <Kpi label={t('Accepted Proposal')} value={sales_proposals.kpi.accepted} icon={CheckCircle} tone="green" />
                        <Kpi label={t('Converted to Invoice')} value={sales_proposals.kpi.converted} icon={Receipt} tone="orange" />
                        <Kpi label={t('Win Rate')} value={`${sales_proposals.kpi.win_rate}%`} icon={TrendingUp} tone="green" />
                    </div>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-md">{t('Proposal Pipeline')}</CardTitle>
                        </CardHeader>
                        <CardContent className="h-72">
                            {sales_proposals.funnel?.length > 0 ? (
                                <BarChart
                                    data={sales_proposals.funnel.map((f: any) => ({
                                        status: f.status,
                                        'Count': f.count,
                                        'Total Value': f.total_value,
                                        'Converted Count': f.converted_count
                                    }))}
                                    dataKey="Count"
                                    xAxisKey="status"
                                    color="#f59e0b"
                                    height={250}
                                    showTooltip
                                />
                            ) : (
                                <div className="flex items-center justify-center h-full text-muted-foreground text-sm">
                                    {t('No proposal data')}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2">
                            <CardTitle className="text-md">{t('All Sales Proposals')}</CardTitle>
                            <div className="flex items-center gap-2">
                                <Select
                                    value={proposalsPerPage}
                                    onValueChange={(v: string) => {
                                        setProposalsPerPage(v);
                                        setProposalsPage(1);
                                    }}
                                >
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
                                    data={allProposalsData}
                                    columns={[
                                        { key: 'proposal_number', header: t('Proposal #') },
                                        { key: 'proposal_date', header: t('Date'), render: (v: string) => formatDate(v, pageProps) },
                                        { key: 'due_date', header: t('Due Date'), render: (v: string) => formatDate(v, pageProps) },
                                        { key: 'customer_name', header: t('Customer') },
                                        { key: 'subtotal', header: t('Subtotal'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'tax_amount', header: t('Tax'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'discount_amount', header: t('Discount'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'total_amount', header: t('Total'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'status', header: t('Status') },
                                        { key: 'converted_to_invoice', header: t('Converted'), render: (v: boolean) => v ? t('Yes') : t('No') },
                                        { key: 'converted_invoice_number', header: t('Invoice #') },
                                        { key: 'days_pending', header: t('Days Pending') },
                                    ]}
                                    filename="all-proposals"
                                    title={t('All Sales Proposals')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {allProposalsData.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProposalsSort('proposal_number')}>
                                                        {t('Proposal #')} {getProposalsSortIcon('proposal_number')}
                                                    </TableHead>
                                                    <TableHead>{t('Invoice #')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProposalsSort('proposal_date')}>
                                                        {t('Date')} {getProposalsSortIcon('proposal_date')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProposalsSort('due_date')}>
                                                        {t('Due Date')} {getProposalsSortIcon('due_date')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProposalsSort('customer_name')}>
                                                        {t('Customer')} {getProposalsSortIcon('customer_name')}
                                                    </TableHead>
                                                    <TableHead className="text-right">{t('Subtotal')}</TableHead>
                                                    <TableHead className="text-right">{t('Tax')}</TableHead>
                                                    <TableHead className="text-right">{t('Discount')}</TableHead>
                                                    <TableHead className="text-right font-medium">{t('Total')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleProposalsSort('status')}>
                                                        {t('Status')} {getProposalsSortIcon('status')}
                                                    </TableHead>
                                                    <TableHead className="text-center">{t('Converted')}</TableHead>
                                                    <TableHead className="text-right">{t('Days Pending')}</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {proposalsPaginatedData.map((proposal: any) => (
                                                    <TableRow key={proposal.id} className="hover:bg-muted/50">
                                                        <TableCell className="text-sm">
                                                            {pageProps?.auth?.user?.permissions?.includes("view-sales-proposals") && proposal.id ? (
                                                                <Link href={route("sales-proposals.show", proposal.id)} className="text-blue-600 hover:text-blue-700 font-medium">
                                                                    {proposal.proposal_number}
                                                                </Link>
                                                            ) : (
                                                                proposal.proposal_number || "-"
                                                            )}
                                                        </TableCell>
                                                        <TableCell className="text-sm">
                                                            {pageProps?.auth?.user?.permissions?.includes("view-sales-invoices") && proposal.converted_invoice_id ? (
                                                                <Link href={route("sales-invoices.show", proposal.converted_invoice_id)} className="text-blue-600 hover:text-blue-700 font-medium">
                                                                    {proposal.converted_invoice_number}
                                                                </Link>
                                                            ) : (
                                                                proposal.converted_invoice_number || "-"
                                                            )}
                                                        </TableCell>
                                                        <TableCell className="text-sm">{formatDate(proposal.proposal_date, pageProps)}</TableCell>
                                                        <TableCell className="text-sm">{formatDate(proposal.due_date, pageProps)}</TableCell>
                                                        <TableCell className="text-sm">{proposal.customer_name}</TableCell>
                                                        <TableCell className="text-right text-sm">{formatCurrency(proposal.subtotal || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm">{formatCurrency(proposal.tax_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm">{formatCurrency(proposal.discount_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right font-medium">{formatCurrency(proposal.total_amount || 0, pageProps)}</TableCell>
                                                        <TableCell>
                                                            <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize ${getStatusBadgeStyle(proposal.status)}`}>{t(proposal.status)}</span>
                                                        </TableCell>
                                                        <TableCell className="text-center">
                                                            {proposal.converted_to_invoice ? (
                                                                <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800`}>{t('Yes')}</span>
                                                            ) : (
                                                                <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800`}>{t('No')}</span>
                                                            )}
                                                        </TableCell>
                                                        <TableCell className="text-right text-sm">{proposal.days_pending || '-'}</TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t('Showing')} {proposalsFrom} {t('to')} {proposalsTo} {t('of')} {allProposalsData.length} {t('results')}
                                        </span>
                                        {proposalsTotalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setProposalsPage(proposalsPage - 1)}
                                                    disabled={proposalsPage === 1}
                                                    className="h-8 text-sm"
                                                >
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t('Previous')}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getProposalsPageNumbers().map((p, i) =>
                                                        typeof p === 'number' ? (
                                                            <Button
                                                                key={i}
                                                                variant={proposalsPage === p ? 'default' : 'outline'}
                                                                size="sm"
                                                                onClick={() => setProposalsPage(p)}
                                                                className="h-8 w-8 p-0 text-xs"
                                                            >
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setProposalsPage(proposalsPage + 1)}
                                                    disabled={proposalsPage === proposalsTotalPages}
                                                    className="h-8 text-xs"
                                                >
                                                    {t('Next')}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No proposals data')}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 5: PURCHASE INVOICES ANALYTICS ==================== */}
                <TabsContent value="purchases" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-3">
                        <Kpi label={t('Total Purchases')} value={`${formatCurrency(purchase_invoices.kpi.total_purchases || 0, pageProps)}`} icon={ShoppingCart} tone="red" />
                        <Kpi label={t('Outstanding Payables')} value={`${formatCurrency(purchase_invoices.kpi.outstanding_payables || 0, pageProps)}`} icon={Clock} tone="yellow" />
                        <Kpi label={t('Overdue Payables')} value={`${formatCurrency(purchase_invoices.kpi.overdue_payables || 0, pageProps)}`} icon={AlertCircle} tone="orange" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t('Monthly Purchase Volume')}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {purchase_invoices.monthly_purchase?.length > 0 ? (
                                    <BarChart
                                        data={purchase_invoices.monthly_purchase.map((m: any) => ({
                                            month: m.month,
                                            'Paid Amount': parseFloat(m.total_paid) || 0,
                                            'Outstanding Amount': parseFloat(m.total_outstanding) || 0,
                                            total_purchase: m.total_purchase,
                                            debit_notes_applied: m.debit_notes_applied,
                                            invoice_count: m.invoice_count
                                        }))}
                                        xAxisKey="month"
                                        height={250}
                                        bars={[
                                            { dataKey: 'Paid Amount', color: '#10b981', name: t('Paid') },
                                            { dataKey: 'Outstanding Amount', color: '#f59e0b', name: t('Outstanding') },
                                        ]}
                                        showLegend
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">
                                        {t('No purchase data')}
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t('Top 10 Products by Purchase Cost')}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {purchase_invoices.top_products?.length > 0 ? (
                                    <BarChart
                                        data={purchase_invoices.top_products.map((p: any) => ({
                                            product_name: p.product_name,
                                            'Total Cost': parseFloat(p.total_cost) || 0,
                                            total_units_purchased: p.total_units_purchased,
                                            avg_purchase_price: p.avg_purchase_price
                                        }))}
                                        dataKey="Total Cost"
                                        xAxisKey="product_name"
                                        color="#ef4444"
                                        height={250}
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">
                                        {t('No product data')}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap">
                            <CardTitle className="text-md">{t('All Purchase Invoices')}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Select value={purchasesPerPage} onValueChange={(v: string) => { setPurchasesPerPage(v); setPurchasesPage(1); }}>
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
                                    data={allPurchaseInvoicesData}
                                    columns={[
                                        { key: 'invoice_number', header: t('Invoice #') },
                                        { key: 'invoice_date', header: t('Date'), render: (v: string) => formatDate(v, pageProps) },
                                        { key: 'due_date', header: t('Due Date'), render: (v: string) => formatDate(v, pageProps) },
                                        { key: 'vendor_name', header: t('Vendor') },
                                        { key: 'subtotal', header: t('Subtotal'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'tax_amount', header: t('Tax'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'discount_amount', header: t('Discount'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'total_amount', header: t('Total'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'paid_amount', header: t('Paid'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'debit_note_applied', header: t('Debit Notes'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'balance_amount', header: t('Balance'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'status', header: t('Status') },
                                        { key: 'return_count', header: t('Returns') },
                                        { key: 'journal_number', header: t('Journal #') },
                                    ]}
                                    filename="purchase-invoices"
                                    title={t('Purchase Invoices')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {purchasesPaginatedData.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchasesSort('invoice_number')}>
                                                        {t('Invoice #')} {getPurchasesSortIcon('invoice_number')}
                                                    </TableHead>
                                                    <TableHead>{t('Journal #')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchasesSort('invoice_date')}>
                                                        {t('Date')} {getPurchasesSortIcon('invoice_date')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchasesSort('due_date')}>
                                                        {t('Due Date')} {getPurchasesSortIcon('due_date')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchasesSort('vendor_name')}>
                                                        {t('Vendor')} {getPurchasesSortIcon('vendor_name')}
                                                    </TableHead>
                                                    <TableHead className="text-right">{t('Subtotal')}</TableHead>
                                                    <TableHead className="text-right">{t('Tax')}</TableHead>
                                                    <TableHead className="text-right">{t('Discount')}</TableHead>
                                                    <TableHead className="text-right font-medium">{t('Total')}</TableHead>
                                                    <TableHead className="text-right">{t('Paid')}</TableHead>
                                                    <TableHead className="text-right">{t('Balance')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handlePurchasesSort('status')}>
                                                        {t('Status')} {getPurchasesSortIcon('status')}
                                                    </TableHead>
                                                    <TableHead className="text-center">{t('Returns')}</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {purchasesPaginatedData.map((invoice: any) => (
                                                    <TableRow key={invoice.id} className="hover:bg-muted/50" onClick={() => setExpandedPurchaseId(expandedPurchaseId === invoice.id ? null : invoice.id)}>
                                                        <TableCell className="text-sm">
                                                            {pageProps?.auth?.user?.permissions?.includes("view-purchase-invoices") && invoice.id ? (
                                                                <Link href={route("purchase-invoices.show", invoice.id)} className="text-blue-600 hover:text-blue-700 font-medium">
                                                                    {invoice.invoice_number}
                                                                </Link>
                                                            ) : (
                                                                invoice.invoice_number || "-"
                                                            )}
                                                        </TableCell>
                                                        <TableCell className="text-sm">{invoice.journal_number || '-'}</TableCell>
                                                        <TableCell className="text-sm">{formatDate(invoice.invoice_date, pageProps)}</TableCell>
                                                        <TableCell className="text-sm">{formatDate(invoice.due_date, pageProps)}</TableCell>
                                                        <TableCell className="text-sm">{invoice.vendor_name}</TableCell>
                                                        <TableCell className="text-right text-sm">{formatCurrency(invoice.subtotal || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm">{formatCurrency(invoice.tax_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm">{formatCurrency(invoice.discount_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right font-medium">{formatCurrency(invoice.total_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm text-green-600">{formatCurrency(invoice.paid_amount || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm text-yellow-600">{formatCurrency(invoice.balance_amount || 0, pageProps)}</TableCell>
                                                        <TableCell>
                                                            <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize ${getStatusBadgeStyle(invoice.status)}`}>{t(invoice.status)}</span>
                                                        </TableCell>
                                                        <TableCell className="text-center text-sm">{invoice.return_count || 0}</TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t('Showing')} {purchasesFrom} {t('to')} {purchasesTo} {t('of')} {filteredPurchasesData.length} {t('results')}
                                        </span>
                                        {purchasesTotalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setPurchasesPage(purchasesPage - 1)}
                                                    disabled={purchasesPage === 1}
                                                    className="h-8 text-sm"
                                                >
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t('Previous')}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getPurchasesPageNumbers().map((p, i) =>
                                                        typeof p === 'number' ? (
                                                            <Button
                                                                key={i}
                                                                variant={purchasesPage === p ? 'default' : 'outline'}
                                                                size="sm"
                                                                onClick={() => setPurchasesPage(p)}
                                                                className="h-8 w-8 p-0 text-xs"
                                                            >
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setPurchasesPage(purchasesPage + 1)}
                                                    disabled={purchasesPage === purchasesTotalPages}
                                                    className="h-8 text-xs"
                                                >
                                                    {t('Next')}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No purchase invoices')}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 6: CUSTOMER REVENUE & CLV ANALYTICS ==================== */}
                <TabsContent value="customers" className="mt-0 space-y-4">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                        <Kpi label={t('Total Customers')} value={customer_analytics.kpi.total_customers} icon={Users} tone="blue" />
                        <Kpi label={t('New This Month')} value={customer_analytics.kpi.new_customers_month} icon={CheckCircle} tone="green" />
                        <Kpi label={t('Avg CLV')} value={`${formatCurrency(customer_analytics.kpi.avg_clv || 0, pageProps)}`} icon={DollarSign} tone="green" />
                        <Kpi label={t('At Risk Customers')} value={customer_analytics.kpi.at_risk_customers} icon={AlertCircle} tone="red" />
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t('Customer Segmentation by Revenue')}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {customer_analytics.segmentation?.length > 0 ? (
                                    <PieChart
                                        data={customer_analytics.segmentation.map((s: any) => ({
                                            ...s,
                                            name: s.segment,
                                            value: s.customer_count,
                                            color: s.segment === 'VIP' ? '#ec4899' : s.segment === 'High Value' ? '#f59e0b' : s.segment === 'Medium Value' ? '#3b82f6' : '#6b7280'
                                        }))}
                                        dataKey="value"
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
                                            height={250}
                                        />
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-md">{t('Top 10 Customers by Revenue')}</CardTitle>
                            </CardHeader>
                            <CardContent className="h-72">
                                {customer_analytics.top_customers?.length > 0 ? (
                                    <BarChart
                                        data={customer_analytics.top_customers.map((c: any) => ({
                                            customer_name: c.customer_name,
                                            'Total Revenue': parseFloat(c.total_revenue) || 0,
                                            total_invoices: c.total_invoices,
                                            total_paid: c.total_paid,
                                            outstanding: c.outstanding
                                        }))}
                                        dataKey="Total Revenue"
                                        xAxisKey="customer_name"
                                        color="#10b981"
                                        height={250}
                                    />
                                ) : (
                                    <div className="flex items-center justify-center h-full text-muted-foreground text-sm">
                                        {t('No customer data')}
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between gap-2 flex-wrap">
                            <CardTitle className="text-md">{t('Complete Customer Revenue List')}</CardTitle>
                            <div className="flex items-center gap-2 flex-wrap">
                                <Select value={customersPerPage} onValueChange={(v: string) => { setCustomersPerPage(v); setCustomersPage(1); }}>
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
                                    data={filteredCustomersData}
                                    columns={[
                                        { key: 'customer_name', header: t('Customer') },
                                        { key: 'total_invoices', header: t('Invoices') },
                                        { key: 'total_revenue', header: t('Revenue'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'total_paid', header: t('Paid'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'outstanding', header: t('Outstanding'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'avg_invoice_value', header: t('Avg Invoice'), render: (v: number) => formatCurrency(v || 0, pageProps) },
                                        { key: 'last_purchase_date', header: t('Last Purchase'), render: (v: string) => formatDate(v, pageProps) },
                                        { key: 'days_since_last_purchase', header: t('Days Since') },
                                        { key: 'segment', header: t('Segment') },
                                        { key: 'risk_status', header: t('Risk Status') },
                                    ]}
                                    filename="customer-revenue-list"
                                    title={t('Customer Revenue List')}
                                />
                            </div>
                        </CardHeader>
                        <CardContent className="p-0">
                            {filteredCustomersData.length > 0 ? (
                                <>
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleCustomersSort('customer_name')}>
                                                        {t('Customer')} {getCustomersSortIcon('customer_name')}
                                                    </TableHead>
                                                    <TableHead className="text-right">{t('Invoices')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50 text-right" onClick={() => handleCustomersSort('total_revenue')}>
                                                        {t('Revenue')} {getCustomersSortIcon('total_revenue')}
                                                    </TableHead>
                                                    <TableHead className="text-right">{t('Paid')}</TableHead>
                                                    <TableHead className="text-right">{t('Outstanding')}</TableHead>
                                                    <TableHead className="text-right">{t('Avg Invoice')}</TableHead>
                                                    <TableHead className="text-right">{t('Last Purchase')}</TableHead>
                                                    <TableHead className="text-right">{t('Days Since')}</TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleCustomersSort('segment')}>
                                                        {t('Segment')} {getCustomersSortIcon('segment')}
                                                    </TableHead>
                                                    <TableHead className="cursor-pointer select-none hover:bg-muted/50" onClick={() => handleCustomersSort('risk_status')}>
                                                        {t('Status')} {getCustomersSortIcon('risk_status')}
                                                    </TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {customersPaginatedData.map((customer: any) => (
                                                    <TableRow key={customer.customer_id} className="hover:bg-muted/50 " onClick={() => setExpandedCustomerId(expandedCustomerId === customer.customer_id ? null : customer.customer_id)}>
                                                        <TableCell className="font-medium">{customer.customer_name}</TableCell>
                                                        <TableCell className="text-right">{customer.total_invoices}</TableCell>
                                                        <TableCell className="text-right font-medium text-green-600">{formatCurrency(customer.total_revenue || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right">{formatCurrency(customer.total_paid || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-yellow-600">{formatCurrency(customer.outstanding || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right">{formatCurrency(customer.avg_invoice_value || 0, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm">{formatDate(customer.last_purchase_date, pageProps)}</TableCell>
                                                        <TableCell className="text-right text-sm">{customer.days_since_last_purchase} {t('d')}</TableCell>
                                                        <TableCell>
                                                            <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize ${getStatusBadgeStyle(customer.segment)}`}>{t(customer.segment)}</span>
                                                        </TableCell>
                                                        <TableCell>
                                                            <span className={`px-2 py-0.5 rounded-full text-xs font-medium border capitalize ${getStatusBadgeStyle(customer.risk_status)}`}>{t(customer.risk_status)}</span>
                                                        </TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                    <div className="flex items-center justify-between px-4 py-3 border-t">
                                        <span className="text-sm text-muted-foreground">
                                            {t('Showing')} {customersFrom} {t('to')} {customersTo} {t('of')} {filteredCustomersData.length} {t('results')}
                                        </span>
                                        {customersTotalPages > 1 && (
                                            <div className="flex items-center space-x-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setCustomersPage(customersPage - 1)}
                                                    disabled={customersPage === 1}
                                                    className="h-8 text-sm"
                                                >
                                                    <ChevronLeft className="h-4 w-4" />
                                                    {t('Previous')}
                                                </Button>
                                                <div className="flex items-center space-x-1">
                                                    {getCustomersPageNumbers().map((p, i) =>
                                                        typeof p === 'number' ? (
                                                            <Button
                                                                key={i}
                                                                variant={customersPage === p ? 'default' : 'outline'}
                                                                size="sm"
                                                                onClick={() => setCustomersPage(p)}
                                                                className="h-8 w-8 p-0 text-xs"
                                                            >
                                                                {p}
                                                            </Button>
                                                        ) : (
                                                            <span key={i} className="px-1 text-xs text-muted-foreground">...</span>
                                                        )
                                                    )}
                                                </div>
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() => setCustomersPage(customersPage + 1)}
                                                    disabled={customersPage === customersTotalPages}
                                                    className="h-8 text-xs"
                                                >
                                                    {t('Next')}
                                                    <ChevronRight className="h-4 w-4" />
                                                </Button>
                                            </div>
                                        )}
                                    </div>
                                </>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No customer data')}</div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>

                {/* ==================== TAB 7: SALES VS PURCHASE COMPARISON ==================== */}
                <TabsContent value="comparison" className="mt-0 space-y-4">
                    {/* KPI Cards */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                        <div className="bg-green-50 dark:bg-green-950/30 rounded-xl p-4 border border-green-100 dark:border-green-900/40">
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-xs font-medium text-green-700 dark:text-green-400">{t('Gross Profit')}</span>
                                <div className="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center">
                                    <BarChart3 className="h-4 w-4 text-green-600 dark:text-green-400" />
                                </div>
                            </div>
                            <div className="text-2xl font-bold text-green-700 dark:text-green-300">
                                {formatCurrency(sales_vs_purchase.kpi.gross_profit || 0, pageProps)}
                            </div>
                            <span className="inline-block mt-1.5 text-[11px] font-medium bg-green-100 dark:bg-green-900/60 text-green-700 dark:text-green-300 px-2 py-0.5 rounded-full">
                                ▲ {t('Revenue surplus')}
                            </span>
                        </div>

                        <div className="bg-blue-50 dark:bg-blue-950/30 rounded-xl p-4 border border-blue-100 dark:border-blue-900/40">
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-xs font-medium text-blue-700 dark:text-blue-400">{t('Gross Margin %')}</span>
                                <div className="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                                    <Target className="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                </div>
                            </div>
                            <div className="text-2xl font-bold text-blue-700 dark:text-blue-300">
                                {sales_vs_purchase.kpi.gross_margin}%
                            </div>
                            <span className="inline-block mt-1.5 text-[11px] font-medium bg-blue-100 dark:bg-blue-900/60 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded-full">
                                {t('Healthy margin')}
                            </span>
                        </div>

                        <div className="bg-purple-50 dark:bg-purple-950/30 rounded-xl p-4 border border-purple-100 dark:border-purple-900/40">
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-xs font-medium text-purple-700 dark:text-purple-400">{t('Revenue-to-Cost Ratio')}</span>
                                <div className="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center">
                                    <BarChart3 className="h-4 w-4 text-purple-600 dark:text-purple-400" />
                                </div>
                            </div>
                            <div className="text-2xl font-bold text-purple-700 dark:text-purple-300">
                                {sales_vs_purchase.kpi.revenue_to_cost_ratio}
                            </div>
                            <span className="inline-block mt-1.5 text-[11px] font-medium bg-purple-100 dark:bg-purple-900/60 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded-full">
                                {t('Revenue per cost unit')}
                            </span>
                        </div>

                        <div className="bg-orange-50 dark:bg-orange-950/30 rounded-xl p-4 border border-orange-100 dark:border-orange-900/40">
                            <div className="flex items-center justify-between mb-2">
                                <span className="text-xs font-medium text-orange-700 dark:text-orange-400">{t('Net Returns Impact %')}</span>
                                <div className="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/50 flex items-center justify-center">
                                    <Receipt className="h-4 w-4 text-orange-600 dark:text-orange-400" />
                                </div>
                            </div>
                            <div className="text-2xl font-bold text-orange-700 dark:text-orange-300">
                                {sales_vs_purchase.kpi.net_returns_impact}%
                            </div>
                            <span className="inline-block mt-1.5 text-[11px] font-medium bg-orange-100 dark:bg-orange-900/60 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded-full">
                                ⚠ {t('Needs attention')}
                            </span>
                        </div>
                    </div>
                    <Card>
                        <CardHeader className="pb-2">
                            <CardTitle className="text-md">{t('Monthly Sales vs Purchase Comparison')}</CardTitle>
                        </CardHeader>
                        <CardContent className="h-80">
                            {sales_vs_purchase.monthly_comparison?.length > 0 ? (
                                <BarChart
                                    data={sales_vs_purchase.monthly_comparison.map((m: any) => ({
                                        month: m.month,
                                        'Sales Revenue': parseFloat(m.sales_revenue) || 0,
                                        'Purchase Cost': parseFloat(m.purchase_cost) || 0,
                                        'Gross Profit': parseFloat(m.gross_profit) || 0,
                                        gross_margin: m.gross_margin
                                    }))}
                                    xAxisKey="month"
                                    height={280}
                                    bars={[
                                        { dataKey: 'Sales Revenue', color: '#3b82f6', name: t('Sales Revenue') },
                                        { dataKey: 'Purchase Cost', color: '#ef4444', name: t('Purchase Cost') },
                                        { dataKey: 'Gross Profit', color: '#10b981', name: t('Gross Profit') },
                                    ]}
                                    showLegend
                                />
                            ) : (
                                <div className="flex items-center justify-center h-full text-muted-foreground text-sm">
                                    {t('No comparison data')}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </AuthenticatedLayout>
    );
}