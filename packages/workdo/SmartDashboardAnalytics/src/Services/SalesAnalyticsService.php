<?php

namespace Workdo\SmartDashboardAnalytics\Services;

use Illuminate\Support\Facades\DB;

class SalesAnalyticsService
{
    public function getSalesData()
    {
        return [
            'crm_pipeline' => $this->getCrmPipeline(),
            'deal_pipeline' => $this->getDealPipeline(),
            'sales_invoices' => $this->getSalesInvoices(),
            'sales_proposals' => $this->getSalesProposals(),
            'purchase_invoices' => $this->getPurchaseInvoices(),
            'customer_analytics' => $this->getCustomerAnalytics(),
            'sales_vs_purchase' => $this->getSalesVsPurchase(),
        ];
    }

    private function getCrmPipeline()
    {
        $createdBy = creatorId();

        $activeLeads = DB::table('leads')
            ->where('created_by', $createdBy)
            ->where('is_active', 1)
            ->where('is_converted', 0)
            ->count();

        $activeDealCount = DB::table('deals')
            ->where('created_by', $createdBy)
            ->where('is_active', 0)
            ->count();

        $pipelineValue = DB::table('deals')
            ->where('created_by', $createdBy)
            ->where('is_active', 0)
            ->sum('price') ?? 0;

        $totalLeads = DB::table('leads')
            ->where('created_by', $createdBy)
            ->count();

        $convertedLeads = DB::table('leads')
            ->where('created_by', $createdBy)
            ->where('is_converted', '!=', 0)
            ->count();

        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0;

        // Lead funnel by stage - simplified
        $leadFunnel = DB::select('
            SELECT ls.id, ls.name as stage_name, ls.order, COUNT(l.id) as lead_count,
                ROUND(COALESCE(COUNT(l.id), 0) / NULLIF((SELECT COUNT(*) FROM leads WHERE created_by = ? AND is_active = 1), 0) * 100, 2) as stage_pct
            FROM lead_stages ls
            LEFT JOIN leads l ON l.stage_id = ls.id AND l.is_active = 1 AND l.created_by = ?
            WHERE ls.created_by = ?
            GROUP BY ls.id, ls.name, ls.order
            ORDER BY ls.order ASC
        ', [$createdBy, $createdBy, $createdBy]);

        // Leads by source - sources stored as comma-separated string
        $leadsBySource = DB::select('
            SELECT s.id, s.name as source_name,
                COUNT(l.id) as total_leads,
                SUM(CASE WHEN l.is_converted != 0 THEN 1 ELSE 0 END) as converted,
                ROUND(COALESCE(SUM(CASE WHEN l.is_converted != 0 THEN 1 ELSE 0 END) / NULLIF(COUNT(l.id), 0), 0) * 100, 2) as conversion_rate
            FROM sources s
            LEFT JOIN leads l ON FIND_IN_SET(s.id, l.sources) > 0 AND l.created_by = ?
            WHERE s.created_by = ?
            GROUP BY s.id, s.name
            HAVING total_leads > 0
            ORDER BY total_leads DESC
        ', [$createdBy, $createdBy]);

        // Monthly lead volume trend
        $monthlyLeads = DB::select('
            SELECT DATE_FORMAT(created_at, "%Y-%m") as month,
                COUNT(*) as total_leads,
                SUM(CASE WHEN is_converted != 0 THEN 1 ELSE 0 END) as converted_leads
            FROM leads
            WHERE created_by = ? AND created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY month
            ORDER BY month
        ', [$createdBy]);

        // All leads with activity counts
        $allLeads = DB::table('leads as l')
            ->leftJoin('lead_stages as ls', 'l.stage_id', '=', 'ls.id')
            ->leftJoin('pipelines as p', 'l.pipeline_id', '=', 'p.id')
            ->leftJoin('users as u', 'l.user_id', '=', 'u.id')
            ->where('l.created_by', $createdBy)
            ->select(
                'l.id', 'l.name as lead_name', 'l.email', 'l.phone', 'l.subject',
                'ls.name as stage_name', 'p.name as pipeline_name',
                'u.name as assigned_to', 'l.date as lead_date',
                'l.is_active', 'l.is_converted',
                DB::raw('(SELECT COUNT(*) FROM lead_tasks WHERE lead_id = l.id) as task_count'),
                DB::raw('(SELECT COUNT(*) FROM lead_calls WHERE lead_id = l.id) as call_count'),
                DB::raw('(SELECT COUNT(*) FROM lead_emails WHERE lead_id = l.id) as email_count'),
                'l.notes', 'l.created_at'
            )
            ->orderBy('l.created_at', 'desc')
            ->paginate(50);

        return [
            'kpi' => [
                'active_leads' => $activeLeads,
                'active_deals' => $activeDealCount,
                'pipeline_value' => round($pipelineValue, 2),
                'conversion_rate' => $conversionRate,
            ],
            'lead_funnel' => $leadFunnel,
            'leads_by_source' => $leadsBySource,
            'monthly_leads' => $monthlyLeads,
            'all_leads' => $allLeads,
        ];
    }

    private function getDealPipeline()
    {
        $createdBy = creatorId();

        $totalDeals = DB::table('deals')
            ->where('created_by', $createdBy)
            ->where('is_active', 0)
            ->count();

        $totalValue = DB::table('deals')
            ->where('created_by', $createdBy)
            ->where('is_active', 0)
            ->sum('price') ?? 0;

        $avgDealSize = DB::table('deals')
            ->where('created_by', $createdBy)
            ->where('is_active', 0)
            ->avg('price') ?? 0;

        // Deal status: 1 = won
        $wonDeals = DB::table('deals')
            ->where('created_by', $createdBy)
            ->where('status', 'Won')
            ->count();

        // Total active deals for win rate calculation
        $totalActiveClosed = DB::table('deals')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['Active', 'Won', 'Loss']) // 0=open, 1=won, 2=lost
            ->count();

        $winRate = $totalActiveClosed > 0 ? round(($wonDeals / $totalActiveClosed) * 100, 2) : 0;

        // Deals by stage
        $dealsByStage = DB::select('
            SELECT ds.id, ds.name as stage_name, ds.order, COUNT(d.id) as deal_count,
                COALESCE(SUM(d.price), 0) as total_value,
                COALESCE(AVG(d.price), 0) as avg_value
            FROM deal_stages ds
            LEFT JOIN deals d ON d.stage_id = ds.id AND d.is_active = 0 AND d.created_by = ?
            WHERE ds.created_by = ?
            GROUP BY ds.id, ds.name, ds.order
            ORDER BY ds.order ASC
        ', [$createdBy, $createdBy]);

        // Deals by pipeline - only pipelines with active deals
        $dealsByPipeline = DB::select('
            SELECT p.id, p.name as pipeline_name,
                COUNT(d.id) as deal_count,
                COALESCE(SUM(d.price), 0) as total_value
            FROM pipelines p
            LEFT JOIN deals d ON d.pipeline_id = p.id AND d.is_active = 0 AND d.created_by = ?
            WHERE p.created_by = ?
            GROUP BY p.id, p.name
            HAVING COUNT(d.id) > 0
            ORDER BY total_value DESC
        ', [$createdBy, $createdBy]);

        // All deals with activity counts and client information
        $allDeals = DB::table('deals as d')
            ->leftJoin('deal_stages as ds', 'd.stage_id', '=', 'ds.id')
            ->leftJoin('pipelines as p', 'd.pipeline_id', '=', 'p.id')
            ->leftJoin('users as u', 'd.creator_id', '=', 'u.id')
            ->leftJoin('client_deals as cd', 'cd.deal_id', '=', 'd.id')
            ->leftJoin('users as cu', 'cd.client_id', '=', 'cu.id')
            ->where('d.created_by', $createdBy)
            ->select(
                'd.id', 'd.name as deal_name', 'd.price as deal_value',
                'ds.name as stage_name', 'p.name as pipeline_name',
                DB::raw('CASE WHEN d.status = 0 THEN "Open" WHEN d.status = 1 THEN "Won" WHEN d.status = 2 THEN "Lost" ELSE "Unknown" END as status'),
                'u.name as assigned_to', 'd.phone', 'd.notes',
                DB::raw('GROUP_CONCAT(DISTINCT cu.name SEPARATOR ", ") as client_names'),
                DB::raw('(SELECT COUNT(*) FROM deal_tasks WHERE deal_id = d.id) as task_count'),
                DB::raw('(SELECT COUNT(*) FROM deal_calls WHERE deal_id = d.id) as call_count'),
                DB::raw('(SELECT COUNT(*) FROM deal_emails WHERE deal_id = d.id) as email_count'),
                DB::raw('DATEDIFF(CURDATE(), d.created_at) as age_days'),
                'd.created_at'
            )
            ->groupBy('d.id')
            ->orderBy('d.created_at', 'desc')
            ->paginate(50);

        return [
            'kpi' => [
                'total_deals' => $totalDeals,
                'pipeline_value' => round($totalValue, 2),
                'win_rate' => $winRate,
                'avg_deal_size' => round($avgDealSize, 2),
            ],
            'deals_by_stage' => $dealsByStage,
            'deals_by_pipeline' => $dealsByPipeline,
            'all_deals' => $allDeals,
        ];
    }

    private function getSalesInvoices()
    {
        $createdBy = creatorId();

        // KPI: Total Sales Revenue (paid invoices)
        $totalRevenue = DB::table('sales_invoices')
            ->where('created_by', $createdBy)
            ->where('status', 'paid')
            ->sum('total_amount') ?? 0;

        // KPI: Outstanding Amount (posted, partial, overdue)
        $outstanding = DB::table('sales_invoices')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['posted', 'partial', 'overdue'])
            ->sum('balance_amount') ?? 0;

        // KPI: Overdue Invoices Count
        $overdue = DB::table('sales_invoices')
            ->where('created_by', $createdBy)
            ->where('status', 'overdue')
            ->count();

            $overdue = DB::table('sales_invoices')
            ->where('created_by', $createdBy)
            ->where('status', 'posted')
            ->whereDate('due_date', '<=', now()->toDateString())
            ->count();

        // KPI: Proposal Conversion Rate
        $totalProposals = DB::table('sales_quotes')
            ->where('created_by', $createdBy)
            ->count();
        
        $convertedProposals = DB::table('sales_quotes')
            ->where('created_by', $createdBy)
            ->where('status', 'Accepted')
            ->count();
        
        $proposalConversionRate = $totalProposals > 0 ? round(($convertedProposals / $totalProposals) * 100, 2) : 0;

        // Monthly revenue breakdown
        $monthlyRevenue = DB::select('
            SELECT DATE_FORMAT(si.invoice_date, "%Y-%m") as month,
                COUNT(si.id) as invoice_count,
                SUM(si.subtotal) as subtotal,
                SUM(si.tax_amount) as tax_revenue,
                SUM(si.discount_amount) as total_discounts,
                SUM(si.total_amount) as gross_revenue,
                SUM(si.paid_amount) as collected,
                SUM(si.balance_amount) as outstanding
            FROM sales_invoices si
            WHERE si.created_by = ? AND si.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND si.status != "draft"
            GROUP BY month
            ORDER BY month
        ', [$createdBy]);

        // Status distribution
        $statusDistribution = DB::table('sales_invoices')
            ->where('created_by', $createdBy)
            ->selectRaw('status, COUNT(*) as count, SUM(total_amount) as total_value')
            ->groupBy('status')
            ->get();

        // Top 10 products by sales revenue
        $topProducts = DB::select('
            SELECT psi.name as product_name,
                SUM(sii.quantity) as total_units_sold,
                SUM(sii.total_amount) as total_revenue,
                AVG(sii.unit_price) as avg_selling_price,
                SUM(sii.discount_amount) as total_discounts,
                SUM(sii.tax_amount) as total_tax
            FROM sales_invoice_items sii
            JOIN sales_invoices si ON sii.invoice_id = si.id
            JOIN product_service_items psi ON sii.product_id = psi.id
            WHERE si.created_by = ? AND si.status IN ("posted", "partial", "paid")
                AND si.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY psi.id, psi.name
            ORDER BY total_revenue DESC
            LIMIT 10
        ', [$createdBy]);

        // All invoices with return count and journal reference
        $allInvoices = DB::table('sales_invoices as si')
            ->leftJoin('users as cu', 'si.customer_id', '=', 'cu.id')
            ->leftJoin('sales_invoice_return_items as siri', 'si.id', '=', 'siri.return_id')
            ->leftJoin('users as u', 'si.creator_id', '=', 'u.id')
            ->where('si.created_by', $createdBy)
            ->select(
                'si.id', 'si.invoice_number', 'si.invoice_date', 'si.due_date', 'siri.return_quantity',
                'cu.name as customer_name', 'si.type', 'si.subtotal', 'si.tax_amount',
                'si.discount_amount', 'si.total_amount', 'si.paid_amount', 'si.balance_amount',
                'si.status', 'si.payment_terms', 'u.name as created_by_name',
                DB::raw('(SELECT je.journal_number FROM journal_entries je WHERE je.reference_type = "sales_invoice" AND je.reference_id = si.id LIMIT 1) as journal_number'),
                DB::raw('(SELECT COUNT(*) FROM sales_invoice_returns WHERE original_invoice_id = si.id) as return_count'),
                'si.created_at'
            )
            ->orderBy('si.invoice_number', 'desc')
            ->paginate(50);

        return [
            'kpi' => [
                'total_revenue' => round($totalRevenue, 2),
                'outstanding' => round($outstanding, 2),
                'overdue_count' => $overdue,
                'proposal_conversion_rate' => $proposalConversionRate,
            ],
            'monthly_revenue' => $monthlyRevenue,
            'status_distribution' => $statusDistribution,
            'top_products' => $topProducts,
            'invoices' => $allInvoices,
        ];
    }

    private function getSalesProposals()
    {
        $createdBy = creatorId();

        // KPI: Total Proposals Sent
        $totalSent = DB::table('sales_proposals')
            ->where('created_by', $createdBy)
            ->where('status', 'sent')
            ->count();

        // KPI: Accepted Proposals
        $acceptedCount = DB::table('sales_proposals')
            ->where('created_by', $createdBy)
            ->where('status', 'accepted')
            ->count();

        // KPI: Converted to Invoice
        $convertedCount = DB::table('sales_proposals')
            ->where('created_by', $createdBy)
            ->where('converted_to_invoice', 1)
            ->count();

        // KPI: Win Rate %
        $winRate = $totalSent > 0 ? round(($acceptedCount / $totalSent) * 100, 2) : 0;

        // Funnel Chart: Proposal Pipeline
        $proposalFunnel = DB::select('
            SELECT sp.status,
                COUNT(sp.id) as count,
                COALESCE(SUM(sp.total_amount), 0) as total_value,
                SUM(CASE WHEN sp.converted_to_invoice = 1 THEN 1 ELSE 0 END) as converted_count
            FROM sales_proposals sp
            WHERE sp.created_by = ?
            GROUP BY sp.status
            ORDER BY FIELD(sp.status, "draft", "sent", "accepted", "rejected")
        ', [$createdBy]);

        // All Sales Proposals with full details
        $allProposals = DB::table('sales_proposals as sp')
            ->leftJoin('users as cu', 'sp.customer_id', '=', 'cu.id')
            ->leftJoin('users as u', 'sp.creator_id', '=', 'u.id')
            ->leftJoin('sales_invoices as si', 'sp.invoice_id', '=', 'si.id')
            ->where('sp.created_by', $createdBy)
            ->select(
                'sp.id', 'sp.proposal_number', 'sp.proposal_date', 'sp.due_date',
                'cu.name as customer_name',
                'sp.subtotal', 'sp.tax_amount', 'sp.discount_amount', 'sp.total_amount',
                'sp.status', 'sp.converted_to_invoice',
                'si.invoice_number as converted_invoice_number',
                'sp.invoice_id as converted_invoice_id',
                'sp.payment_terms', 'sp.notes',
                DB::raw('CASE WHEN sp.status = "sent" THEN DATEDIFF(CURDATE(), sp.proposal_date) ELSE NULL END as days_pending'),
                'u.name as created_by_name', 'sp.created_at'
            )
            ->orderBy('sp.proposal_number', 'desc')
            ->paginate(50);

        return [
            'kpi' => [
                'total_sent' => $totalSent,
                'accepted' => $acceptedCount,
                'converted' => $convertedCount,
                'win_rate' => $winRate,
            ],
            'funnel' => $proposalFunnel,
            'proposals' => $allProposals,
        ];
    }

    private function getPurchaseInvoices()
    {
        $createdBy = creatorId();

        $totalPurchases = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->where('status', '!=', 'draft')
            ->sum('total_amount');

        $outstandingPayables = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['posted', 'partial'])
            ->sum('balance_amount');

        $overduePayables = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();

        // Monthly purchase breakdown with debit notes
        $monthlyPurchase = DB::select('
            SELECT DATE_FORMAT(pi.invoice_date, "%Y-%m") as month,
                COUNT(pi.id) as invoice_count,
                SUM(pi.total_amount) as total_purchase,
                SUM(pi.paid_amount) as total_paid,
                SUM(pi.balance_amount) as total_outstanding,
                SUM(COALESCE(pi.debit_note_applied, 0)) as debit_notes_applied
            FROM purchase_invoices pi
            WHERE pi.created_by = ? AND pi.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND pi.status != "draft"
            GROUP BY month
            ORDER BY month
        ', [$createdBy]);

        // Top 10 products by purchase volume
        $topProductsByPurchase = DB::select('
            SELECT psi.name as product_name,
                SUM(pii.quantity) as total_units_purchased,
                SUM(pii.total_amount) as total_cost,
                AVG(pii.unit_price) as avg_purchase_price,
                SUM(pii.discount_amount) as total_discounts,
                SUM(pii.tax_amount) as total_tax
            FROM purchase_invoice_items pii
            JOIN purchase_invoices pi ON pii.invoice_id = pi.id
            JOIN product_service_items psi ON pii.product_id = psi.id
            WHERE pi.created_by = ? AND pi.status IN ("posted", "partial", "paid")
                AND pi.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY psi.id, psi.name
            ORDER BY total_cost DESC
            LIMIT 10
        ', [$createdBy]);

        // All purchase invoices with full details
        $allInvoices = DB::table('purchase_invoices as pi')
            ->leftJoin('users as vu', 'pi.vendor_id', '=', 'vu.id')
            ->leftJoin('users as u', 'pi.creator_id', '=', 'u.id')
            ->where('pi.created_by', $createdBy)
            ->select(
                'pi.id', 'pi.invoice_number', 'pi.invoice_date', 'pi.due_date',
                'vu.name as vendor_name', 'pi.subtotal', 'pi.tax_amount', 'pi.discount_amount',
                'pi.total_amount', 'pi.paid_amount', 'pi.debit_note_applied', 'pi.balance_amount',
                'pi.status', 'pi.payment_terms',
                DB::raw('(SELECT je.journal_number FROM journal_entries je WHERE je.reference_type = "purchase_invoice" AND je.reference_id = pi.id LIMIT 1) as journal_number'),
                DB::raw('(SELECT COUNT(*) FROM purchase_returns WHERE original_invoice_id = pi.id) as return_count'),
                'u.name as created_by_name', 'pi.created_at'
            )
            ->orderBy('pi.invoice_number', 'desc')
            ->paginate(50);

        return [
            'kpi' => [
                'total_purchases' => round($totalPurchases ?? 0, 2),
                'outstanding_payables' => round($outstandingPayables ?? 0, 2),
                'overdue_payables' => $overduePayables,
            ],
            'monthly_purchase' => $monthlyPurchase,
            'top_products' => $topProductsByPurchase,
            'invoices' => $allInvoices,
        ];
    }

    private function getCustomerAnalytics()
    {
        $createdBy = creatorId();

        $totalCustomers = DB::table('users')->where('type', 'client')->where('created_by', $createdBy)->count();

        // New Customers This Month
        $newCustomersThisMonth = DB::select('
            SELECT COUNT(DISTINCT si.customer_id) as count
            FROM sales_invoices si
            WHERE si.created_by = ? 
                AND si.status IN ("posted", "partial", "paid")
                AND si.customer_id NOT IN (
                    SELECT DISTINCT customer_id FROM sales_invoices 
                    WHERE created_by = ? AND status IN ("posted", "partial", "paid")
                        AND invoice_date < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
                )
                AND MONTH(si.invoice_date) = MONTH(CURDATE())
                AND YEAR(si.invoice_date) = YEAR(CURDATE())
        ', [$createdBy, $createdBy]);
        $newCustomers = (int)($newCustomersThisMonth[0]->count ?? 0);

        // Average CLV
        $clvData = DB::table('sales_invoices as si')
            ->where('si.created_by', $createdBy)
            ->whereIn('si.status', ['posted', 'partial', 'paid'])
            ->selectRaw('AVG(customer_total.revenue) as avg_clv')
            ->joinSub(
                DB::table('sales_invoices')
                    ->where('created_by', $createdBy)
                    ->whereIn('status', ['posted', 'partial', 'paid'])
                    ->selectRaw('customer_id, SUM(total_amount) as revenue')
                    ->groupBy('customer_id'),
                'customer_total',
                'si.customer_id',
                '=',
                'customer_total.customer_id'
            )
            ->first();
        $avgCLV = round($clvData->avg_clv ?? 0, 2);

        // At Risk Customers
        $atRiskCount = DB::select('
            SELECT COUNT(DISTINCT cu.id) as count
            FROM users cu
            WHERE cu.id IN (
                SELECT DISTINCT customer_id FROM sales_invoices 
                WHERE created_by = ? AND status IN ("posted", "partial", "paid")
            )
            AND (SELECT MAX(invoice_date) FROM sales_invoices 
                WHERE customer_id = cu.id AND created_by = ? AND status IN ("posted", "partial", "paid")
                ) < DATE_SUB(CURDATE(), INTERVAL 90 DAY)
        ', [$createdBy, $createdBy]);
        $atRiskCustomers = (int)($atRiskCount[0]->count ?? 0);

        // Customer Segmentation by Revenue
        $segmentationData = DB::select('
            SELECT
                segment,
                COUNT(*) as customer_count,
                SUM(customer_revenue) as segment_revenue
            FROM (
                SELECT
                    customer_id,
                    SUM(total_amount) as customer_revenue,
                    CASE
                        WHEN SUM(total_amount) >= 100000 THEN "VIP"
                        WHEN SUM(total_amount) >= 50000 THEN "High Value"
                        WHEN SUM(total_amount) >= 10000 THEN "Medium Value"
                        ELSE "Low Value"
                    END as segment
                FROM sales_invoices
                WHERE created_by = ?
                    AND status IN ("posted", "partial", "paid")
                GROUP BY customer_id
            ) customer_segments
            GROUP BY segment
            ORDER BY segment_revenue DESC
        ', [$createdBy]);

        // Top 10 Customers by Revenue
        $topCustomers = DB::select('
            SELECT 
                cu.id as customer_id,
                cu.name as customer_name,
                COUNT(si.id) as total_invoices,
                SUM(si.total_amount) as total_revenue,
                SUM(si.paid_amount) as total_paid,
                SUM(si.balance_amount) as outstanding,
                MAX(si.invoice_date) as last_purchase_date,
                DATEDIFF(CURDATE(), MAX(si.invoice_date)) as days_since_last_purchase
            FROM users cu
            JOIN sales_invoices si ON si.customer_id = cu.id
            WHERE si.created_by = ? AND si.status IN ("posted", "partial", "paid")
            GROUP BY cu.id, cu.name
            ORDER BY total_revenue DESC
            LIMIT 10
        ', [$createdBy]);

        // Complete Customer Revenue List
        $allCustomersData = DB::select('
            SELECT 
                cu.id as customer_id,
                cu.name as customer_name,
                COUNT(si.id) as total_invoices,
                SUM(si.total_amount) as total_revenue,
                SUM(si.paid_amount) as total_paid,
                SUM(si.balance_amount) as outstanding,
                ROUND(AVG(si.total_amount), 2) as avg_invoice_value,
                MAX(si.invoice_date) as last_purchase_date,
                DATEDIFF(CURDATE(), MAX(si.invoice_date)) as days_since_last_purchase,
                CASE 
                    WHEN SUM(si.total_amount) >= 100000 THEN "VIP"
                    WHEN SUM(si.total_amount) >= 50000 THEN "High Value"
                    WHEN SUM(si.total_amount) >= 10000 THEN "Medium Value"
                    ELSE "Low Value"
                END as segment,
                CASE 
                    WHEN DATEDIFF(CURDATE(), MAX(si.invoice_date)) > 90 THEN "At Risk"
                    ELSE "Active"
                END as risk_status,
                (SELECT COUNT(*) FROM sales_invoices WHERE customer_id = cu.id AND created_by = ? AND status = "overdue") as overdue_invoices
            FROM users cu
            JOIN sales_invoices si ON si.customer_id = cu.id
            WHERE si.created_by = ? AND si.status IN ("posted", "partial", "paid")
            GROUP BY cu.id, cu.name
            ORDER BY total_revenue DESC
        ', [$createdBy, $createdBy]);
        
        // Manual pagination
        $page = request('page', 1);
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        $allCustomers = collect($allCustomersData)
            ->slice($offset, $perPage)
            ->values();

        return [
            'kpi' => [
                'total_customers' => $totalCustomers,
                'new_customers_month' => $newCustomers,
                'avg_clv' => $avgCLV,
                'at_risk_customers' => $atRiskCustomers,
            ],
            'segmentation' => $segmentationData,
            'top_customers' => $topCustomers,
            'customer_revenue' => [
                'data' => $allCustomers
            ],
        ];
    }

    private function getSalesVsPurchase()
    {
        $createdBy = creatorId();

        // Total Sales Revenue (only paid/completed invoices)
        $salesTotal = DB::table('sales_invoices')
            ->where('created_by', $createdBy)
            ->where('status', 'paid')
            ->sum('total_amount') ?? 0;

        // Total Purchase Cost (only paid/completed invoices)
        $purchaseTotal = DB::table('purchase_invoices')
            ->where('created_by', $createdBy)
            ->where('status', 'paid')
            ->sum('total_amount') ?? 0;

        // Gross Profit = Sales - Purchase
        $grossProfit = $salesTotal - $purchaseTotal;

        // Gross Margin % = (Gross Profit / Sales Revenue) × 100
        $grossMargin = $salesTotal > 0 ? round(($grossProfit / $salesTotal) * 100, 2) : 0;

        // Revenue-to-Cost Ratio = Sales Total / Purchase Total
        $revenueToCostRatio = $purchaseTotal > 0 ? round($salesTotal / $purchaseTotal, 2) : 0;

        // Sales Returns - total amount of returned sales
        $salesReturns = DB::table('sales_invoice_returns')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['approved', 'completed'])
            ->sum('total_amount') ?? 0;

        // Purchase Returns - total amount of returned purchases
        $purchaseReturns = DB::table('purchase_returns')
            ->where('created_by', $createdBy)
            ->whereIn('status', ['approved', 'completed'])
            ->sum('total_amount') ?? 0;

        // Net Returns Impact = (Sales Returns + Purchase Returns) / Gross Profit × 100
        $totalReturns = $salesReturns + $purchaseReturns;
        $netReturnsImpact = ($salesTotal + $purchaseTotal) > 0 ? round(($totalReturns / ($salesTotal + $purchaseTotal)) * 100, 2) : 0;

        // Monthly comparison with grouped bar chart data
        $monthlyComparison = DB::select('
            SELECT DATE_FORMAT(si.invoice_date, "%Y-%m") as month,
                SUM(si.total_amount) as sales_revenue,
                0 as purchase_cost
            FROM sales_invoices si
            WHERE si.created_by = ? 
                AND si.status = "paid"
                AND si.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY month
            UNION ALL
            SELECT DATE_FORMAT(pi.invoice_date, "%Y-%m") as month,
                0 as sales_revenue,
                SUM(pi.total_amount) as purchase_cost
            FROM purchase_invoices pi
            WHERE pi.created_by = ?
                AND pi.status = "paid"
                AND pi.invoice_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            GROUP BY month
        ', [$createdBy, $createdBy]);

        // Aggregate monthly data with gross profit calculation
        $aggregatedMonths = [];
        foreach ($monthlyComparison as $record) {
            $month = $record->month;
            if (!isset($aggregatedMonths[$month])) {
                $aggregatedMonths[$month] = [
                    'month' => $month,
                    'sales_revenue' => 0,
                    'purchase_cost' => 0,
                ];
            }
            $aggregatedMonths[$month]['sales_revenue'] += (float)$record->sales_revenue;
            $aggregatedMonths[$month]['purchase_cost'] += (float)$record->purchase_cost;
        }

        // Calculate gross profit and margin per month
        $finalMonthlyComparison = collect($aggregatedMonths)->map(function ($month) {
            $sales = (float)($month['sales_revenue'] ?? 0);
            $purchase = (float)($month['purchase_cost'] ?? 0);
            $profit = $sales - $purchase;
            $margin = $sales > 0 ? round(($profit / $sales) * 100, 2) : 0;
            return [
                'month' => $month['month'],
                'sales_revenue' => $sales,
                'purchase_cost' => $purchase,
                'gross_profit' => $profit,
                'gross_margin' => $margin,
            ];
        })->sortBy('month')->values();

        return [
            'kpi' => [
                'gross_profit' => round($grossProfit, 2),
                'gross_margin' => $grossMargin,
                'revenue_to_cost_ratio' => $revenueToCostRatio,
                'net_returns_impact' => $netReturnsImpact,
            ],
            'monthly_comparison' => $finalMonthlyComparison,
        ];
    }
}
