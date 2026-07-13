<?php

namespace Workdo\SmartDashboardAnalytics\Database\Seeders;

use Illuminate\Database\Seeder;
use Workdo\LandingPage\Models\MarketplaceSetting;
use Illuminate\Support\Facades\File;

class MarketplaceSettingSeeder extends Seeder
{
    public function run()
    {
        // Get all available screenshots from marketplace directory
        $marketplaceDir = __DIR__ . '/../../marketplace';
        $screenshots = [];
        
        if (File::exists($marketplaceDir)) {
            $files = File::files($marketplaceDir);
            foreach ($files as $file) {
                if (in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'gif', 'webp'])) {
                    $screenshots[] = '/packages/workdo/SmartDashboardAnalytics/src/marketplace/' . $file->getFilename();
                }
            }
        }
        
        sort($screenshots);
        
        MarketplaceSetting::firstOrCreate(['module' => 'SmartDashboardAnalytics'], [
            'module' => 'SmartDashboardAnalytics',
            'title' => 'Smart Dashboard Analytics',
            'subtitle' => 'Comprehensive business intelligence and analytics platform with executive, financial, sales, and operational insights',
            'config_sections' => [
                'sections' => [
                    'hero' => [
                        'variant' => 'hero1',
                        'title' => 'Smart Dashboard Analytics for WorkDo Dash',
                        'subtitle' => 'Powerful business analytics platform combining executive dashboards, financial insights, sales analytics, and operational metrics. Transform your raw business data into actionable intelligence with comprehensive reports, real-time charts, and advanced filtering capabilities.',
                        'primary_button_text' => 'Install Smart Dashboard Analytics',
                        'primary_button_link' => '#install',
                        'secondary_button_text' => 'View Features',
                        'secondary_button_link' => '#features',
                        'image' => '/packages/workdo/SmartDashboardAnalytics/src/marketplace/hero.png'
                    ],
                    'modules' => [
                        'variant' => 'modules1',
                        'title' => 'Smart Dashboard Analytics',
                        'subtitle' => 'Complete Business Intelligence Platform with Executive, Financial, Sales & Operational Analytics'
                    ],
                    'dedication' => [
                        'variant' => 'dedication1',
                        'title' => 'Comprehensive Dashboard Analytics Features',
                        'description' => 'Our Smart Dashboard Analytics module provides real-time business insights across all key operational areas with interactive charts, customizable reports, and advanced data visualization.',
                        'subSections' => [
                            [
                                'title' => 'Executive Overview Dashboard',
                                'description' => 'Comprehensive executive dashboard featuring health scores, KPI cards, and quick insights. Track module summaries for all business areas with data tables for employees, customers, and recent transactions. Perfect for C-level executives and stakeholders requiring high-level business metrics.',
                                'keyPoints' => ['Organization Health Score with status indicators', 'KPI Cards with trending metrics and performance indicators', 'Quick Insights section with actionable business intelligence', 'Module Summary overview of all operational areas', 'Interactive data tables for employees, customers, and transactions'],
                                'screenshot' => '/packages/workdo/SmartDashboardAnalytics/src/marketplace/image1.png'
                            ],
                            [
                                'title' => 'Financial Analytics Dashboard',
                                'description' => 'Advanced financial analytics covering revenue trends, expense analysis, profitability tracking, cash flow management, and journal entries. Includes 12-month revenue trends with dynamic charting, expense breakdown by category, profit margin analysis, cash flow forecasting, and detailed journal entry reporting.',
                                'keyPoints' => ['Revenue Trend analysis with 12-month visualization and all months display', 'Expense Breakdown by category with donut chart analytics', 'Profitability tracking with margin analysis and trend visualization', 'Cash Flow Management with inflows and outflows tracking', 'Complete Journal Entries review with transaction details'],
                                'screenshot' => '/packages/workdo/SmartDashboardAnalytics/src/marketplace/image2.png'
                            ],
                            [
                                'title' => 'Sales & Customer Analytics Dashboard',
                                'description' => 'Comprehensive sales analytics with 7 specialized tabs covering CRM pipeline, deal tracking, sales invoices, proposals, purchases, customer analysis, and sales vs purchase comparison. Features include pipeline visualization, deal status tracking, invoice analytics, proposal management, and customer performance metrics.',
                                'keyPoints' => ['CRM Pipeline visualization with stage-based deal tracking', 'Deal Analytics with value, probability, and status analysis', 'Sales Invoice reports with amount and status filtering', 'Proposal tracking and analytics', 'Customer Analytics with revenue and transaction metrics', 'Sales vs Purchase Comparison with period-based analysis'],
                                'screenshot' => '/packages/workdo/SmartDashboardAnalytics/src/marketplace/image3.png'
                            ],
                            [
                                'title' => 'Operational Analytics Dashboard',
                                'description' => 'Operational metrics across inventory, POS, projects, and purchase/vendor management. Track stock levels, POS performance, project progress with color-coded status indicators, and vendor relationships. Includes purchase summary, vendor performance, AP aging reports, and operational efficiency metrics.',
                                'keyPoints' => ['Inventory & Stock tracking with product analytics', 'POS Analytics with transaction and revenue metrics', 'Project tracking with progress color-coding and task management', 'Purchase & Vendor Management with AP aging and vendor performance', 'Vendor Purchase Summary analysis'],
                                'screenshot' => '/packages/workdo/SmartDashboardAnalytics/src/marketplace/image4.png'
                            ]
                        ]
                    ],
                    'screenshots' => [
                        'variant' => 'screenshots1',
                        'title' => 'Smart Dashboard Analytics in Action',
                        'subtitle' => 'See how comprehensive business intelligence transforms your decision-making process',
                        'images' => $screenshots
                    ],
                    'why_choose' => [
                        'variant' => 'whychoose1',
                        'title' => 'Why Choose Smart Dashboard Analytics?',
                        'subtitle' => 'Transform your business with data-driven insights and comprehensive analytics',
                        'benefits' => [
                            [
                                'title' => 'Executive Dashboard',
                                'description' => 'Health scores, KPI cards, and quick insights for strategic decision-making at a glance.',
                                'icon' => 'BarChart3',
                                'color' => 'blue'
                            ],
                            [
                                'title' => 'Financial Insights',
                                'description' => 'Revenue trends, expense analysis, profitability tracking, and cash flow management.',
                                'icon' => 'TrendingUp',
                                'color' => 'green'
                            ],
                            [
                                'title' => 'Sales Analytics',
                                'description' => 'CRM pipeline, deal tracking, invoice analytics, and customer performance metrics.',
                                'icon' => 'ShoppingCart',
                                'color' => 'purple'
                            ],
                            [
                                'title' => 'Operational Metrics',
                                'description' => 'Inventory, POS, projects, and vendor management analytics in one platform.',
                                'icon' => 'Zap',
                                'color' => 'red'
                            ],
                            [
                                'title' => 'Real-time Visualization',
                                'description' => 'Interactive charts, graphs, and tables with dynamic updates and smart data visualization.',
                                'icon' => 'BarChart',
                                'color' => 'yellow'
                            ],
                            [
                                'title' => 'Advanced Filtering',
                                'description' => 'Comprehensive filtering, date range selection, and custom data aggregation capabilities.',
                                'icon' => 'Filter',
                                'color' => 'indigo'
                            ],
                            [
                                'title' => 'Export Capabilities',
                                'description' => 'Export reports to PDF, Excel, and CSV formats with professional formatting.',
                                'icon' => 'Download',
                                'color' => 'pink'
                            ],
                            [
                                'title' => 'Print Functionality',
                                'description' => 'Print-ready dashboards and reports with optimized layouts and professional appearance.',
                                'icon' => 'Printer',
                                'color' => 'orange'
                            ],
                            [
                                'title' => 'Multi-currency Support',
                                'description' => 'Display financial data in multiple currencies with proper formatting and conversion.',
                                'icon' => 'DollarSign',
                                'color' => 'teal'
                            ],
                            [
                                'title' => 'Date Format Localization',
                                'description' => 'Automatic date formatting based on company settings and regional preferences.',
                                'icon' => 'Calendar',
                                'color' => 'cyan'
                            ],
                            [
                                'title' => 'Data Export & Integration',
                                'description' => 'Seamless data export and integration with other business tools and systems.',
                                'icon' => 'Share2',
                                'color' => 'emerald'
                            ],
                            [
                                'title' => 'Role-based Access',
                                'description' => 'Secure dashboards with role-based permissions and data visibility controls.',
                                'icon' => 'Lock',
                                'color' => 'slate'
                            ]
                        ]
                    ]
                ],
                'section_visibility' => [
                    'header' => true,
                    'hero' => true,
                    'modules' => true,
                    'dedication' => true,
                    'screenshots' => true,
                    'why_choose' => true,
                    'cta' => true,
                    'footer' => true
                ],
                'section_order' => ['header', 'hero', 'modules', 'dedication', 'screenshots', 'why_choose', 'cta', 'footer']
            ]
        ]);
    }
}
