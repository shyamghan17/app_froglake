import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Progress } from '@/components/ui/progress';
import { PieChart } from '@/components/charts/PieChart';
import { LineChart } from '@/components/charts/LineChart';
import { BarChart } from '@/components/charts/BarChart';
import {
    Users, UserCheck, CalendarCheck, Clock, DollarSign,
    TrendingUp, TrendingDown, Briefcase, Target, Star,
    CheckCircle, XCircle, AlertTriangle, ArrowUpRight, ArrowDownRight,
    UserPlus, UserMinus, Activity
} from 'lucide-react';

interface EmployeeOverview {
    kpi: { total_employees: number; present_today: number; total_payroll_cost: number };
    department_distribution: Array<{ department_name: string; employee_count: number }>;
    employee_list: { data: Array<{
        employee_name: string; employee_id: string; department_name: string | null;
        designation_name: string | null; attendance_percentage: number;
        tasks_completed: number; hours_worked: number; basic_salary: number;
    }> };
}

interface AttendanceAnalytics {
    kpi: { overall_attendance: number; pending_leaves: number };
    calendar_heatmap: Array<{
        attendance_date: string; present_count: number; absent_count: number;
        half_day_count: number; total_employees: number; attendance_rate: number;
    }>;
    attendance_trend: Array<{ month: string; attendance_rate: number }>;
    attendance_records: { data: Array<{
        date: string; employee_name: string; department_name: string;
        shift_name: string; clock_in: string; clock_out: string;
        total_hour: number; status: string; punctuality: string;
    }> };
    leave_requests: Array<{
        id: number; employee_name: string; department_name: string;
        leave_type_name: string; start_date: string; end_date: string;
        total_days: number; status: string; reason: string; applied_date: string;
    }>;
}

interface DepartmentComparison {
    department_name: string; employee_count: number;
    total_salary: number; tasks_completed: number; attendance_rate: number;
}

interface PayrollAnalysis {
    kpi: { total_payroll: number; average_salary: number };
    salary_by_department: Array<{
        department_name: string; total_gross_pay: number;
        total_deductions: number; total_net_pay: number;
        employee_count: number; avg_net_pay: number;
    }>;
    payroll_records: { data: Array<{
        payroll_title: string; payroll_frequency: string;
        pay_period_start: string; pay_period_end: string; pay_date: string;
        payroll_status: string; employee_name: string; department_name: string;
        basic_salary: number; total_allowances: number; total_deductions: number;
        gross_pay: number; net_pay: number;
    }> };
}

interface EmployeeRoi {
    employee_name: string; department_name: string | null;
    monthly_salary: number; tasks_completed: number;
    payroll_cost: number; roi_ratio: number;
}

interface TeamPerformanceProps extends Record<string, any> {
    employee_overview: EmployeeOverview;
    attendance_analytics: AttendanceAnalytics;
    department_comparison: DepartmentComparison[];
    payroll_analysis: PayrollAnalysis;
    employee_roi: EmployeeRoi[];
}

export default function TeamPerformance() {
    const { t } = useTranslation();
    const props = usePage<TeamPerformanceProps>().props;
    const { employee_overview, attendance_analytics, department_comparison, payroll_analysis, employee_roi } = props;

    return (
        <AuthenticatedLayout
            breadcrumbs={[
                { label: t('Smart Analytics') },
                { label: t('Team Performance') }
            ]}
            pageTitle={t('Team Performance Dashboard')}
        >
            <Head title={t('Team Performance')} />

            {/* Section 1: Employee Overview */}
            <div className="mb-8">
                <h2 className="text-lg font-semibold mb-4 flex items-center gap-2">
                    <Users className="h-5 w-5 text-blue-600" />
                    {t('Employee Overview')}
                </h2>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">{t('Total Employees')}</CardTitle>
                            <Users className="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{employee_overview.kpi.total_employees}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">{t('Present Today')}</CardTitle>
                            <UserCheck className="h-4 w-4 text-green-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-green-600">{employee_overview.kpi.present_today}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">{t('Total Payroll Cost')}</CardTitle>
                            <DollarSign className="h-4 w-4 text-red-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">${(employee_overview.kpi.total_payroll_cost || 0).toLocaleString()}</div>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-sm">{t('Department Distribution')}</CardTitle>
                        </CardHeader>
                        <CardContent className="h-80">
                            {employee_overview.department_distribution && employee_overview.department_distribution.length > 0 ? (
                                <PieChart
                                    data={employee_overview.department_distribution}
                                    dataKey="employee_count"
                                    nameKey="department_name"
                                    donut
                                    showLegend
                                    showTooltip
                                    height={280}
                                />
                            ) : (
                                <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t('No department data')}</div>
                            )}
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-sm">{t('Top Employees by Attendance')}</CardTitle>
                            <CardDescription>{t('Current month')}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            {employee_overview.employee_list.data && employee_overview.employee_list.data.length > 0 ? (
                                <div className="space-y-2">
                                    {employee_overview.employee_list.data.slice(0, 10).map((emp, idx) => (
                                        <div key={idx} className="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                            <div className="flex items-center gap-2">
                                                <span className="text-xs font-bold text-gray-400 w-5">#{idx + 1}</span>
                                                <div>
                                                    <p className="text-sm font-medium">{emp.employee_name}</p>
                                                    <p className="text-xs text-muted-foreground">{emp.department_name || ''}</p>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-sm font-semibold">{emp.attendance_percentage || 0}%</p>
                                                <p className="text-xs text-muted-foreground">{emp.tasks_completed} {t('tasks')}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No employee data')}</div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* Section 2: Attendance & Leave Analytics */}
            <div className="mb-8">
                <h2 className="text-lg font-semibold mb-4 flex items-center gap-2">
                    <CalendarCheck className="h-5 w-5 text-green-600" />
                    {t('Attendance & Leave Analytics')}
                </h2>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">{t('Overall Attendance')}</CardTitle>
                            <Activity className="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div className={`text-2xl font-bold ${(attendance_analytics.kpi.overall_attendance || 0) >= 80 ? 'text-green-600' : 'text-yellow-600'}`}>
                                {attendance_analytics.kpi.overall_attendance}%
                            </div>
                            <Progress
                                value={attendance_analytics.kpi.overall_attendance || 0}
                                className="h-2 mt-2"
                            />
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">{t('Pending Leave Requests')}</CardTitle>
                            <Clock className="h-4 w-4 text-yellow-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-yellow-600">{attendance_analytics.kpi.pending_leaves}</div>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-sm">{t('Attendance Rate Trend (6 months)')}</CardTitle>
                        </CardHeader>
                        <CardContent className="h-80">
                            {attendance_analytics.attendance_trend && attendance_analytics.attendance_trend.length > 0 ? (
                                <LineChart
                                    data={attendance_analytics.attendance_trend}
                                    dataKey="attendance_rate"
                                    xAxisKey="month"
                                    color="#10b981"
                                    showDots
                                    height={280}
                                />
                            ) : (
                                <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t('No attendance trend data')}</div>
                            )}
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-sm">{t('Recent Leave Requests')}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {attendance_analytics.leave_requests && attendance_analytics.leave_requests.length > 0 ? (
                                <div className="space-y-2">
                                    {attendance_analytics.leave_requests.slice(0, 8).map((leave, idx) => (
                                        <div key={idx} className="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                            <div>
                                                <p className="text-sm font-medium">{leave.employee_name}</p>
                                                <p className="text-xs text-muted-foreground">{leave.leave_type_name}</p>
                                            </div>
                                            <div className="text-right">
                                                <Badge variant={
                                                    leave.status === 'approved' ? 'default' :
                                                    leave.status === 'rejected' ? 'destructive' : 'secondary'
                                                } className="text-xs">
                                                    {leave.status}
                                                </Badge>
                                                <p className="text-xs text-muted-foreground mt-1">{leave.total_days}d</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No leave requests')}</div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* Section 3: Department Comparison */}
            <div className="mb-8">
                <h2 className="text-lg font-semibold mb-4 flex items-center gap-2">
                    <Briefcase className="h-5 w-5 text-purple-600" />
                    {t('Department Performance Comparison')}
                </h2>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-sm">{t('Department Metrics')}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {department_comparison && department_comparison.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="border-b text-left">
                                            <th className="pb-2 font-medium text-muted-foreground">{t('Department')}</th>
                                            <th className="pb-2 font-medium text-muted-foreground text-right">{t('Employees')}</th>
                                            <th className="pb-2 font-medium text-muted-foreground text-right">{t('Total Salary')}</th>
                                            <th className="pb-2 font-medium text-muted-foreground text-right">{t('Tasks')}</th>
                                            <th className="pb-2 font-medium text-muted-foreground text-right">{t('Attendance %')}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {department_comparison.map((dept, idx) => (
                                            <tr key={idx} className="border-b last:border-0">
                                                <td className="py-2 font-medium">{dept.department_name}</td>
                                                <td className="py-2 text-right">{dept.employee_count}</td>
                                                <td className="py-2 text-right">${(dept.total_salary || 0).toLocaleString()}</td>
                                                <td className="py-2 text-right">{dept.tasks_completed}</td>
                                                <td className="py-2 text-right">
                                                    <span className={dept.attendance_rate >= 80 ? 'text-green-600' : 'text-yellow-600'}>
                                                        {dept.attendance_rate}%
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="text-center py-8 text-muted-foreground text-sm">{t('No department data')}</div>
                        )}
                    </CardContent>
                </Card>
            </div>

            {/* Section 4: Payroll Analysis */}
            <div className="mb-8">
                <h2 className="text-lg font-semibold mb-4 flex items-center gap-2">
                    <DollarSign className="h-5 w-5 text-emerald-600" />
                    {t('Payroll & Compensation Analysis')}
                </h2>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">{t('Total Payroll')}</CardTitle>
                            <DollarSign className="h-4 w-4 text-emerald-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">${(payroll_analysis.kpi.total_payroll || 0).toLocaleString()}</div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">{t('Average Salary')}</CardTitle>
                            <Users className="h-4 w-4 text-blue-600" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">${(Math.round(payroll_analysis.kpi.average_salary || 0)).toLocaleString()}</div>
                        </CardContent>
                    </Card>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-sm">{t('Salary by Department')}</CardTitle>
                        </CardHeader>
                        <CardContent className="h-80">
                            {payroll_analysis.salary_by_department && payroll_analysis.salary_by_department.length > 0 ? (
                                <BarChart
                                    data={payroll_analysis.salary_by_department}
                                    dataKey="total_net_pay"
                                    xAxisKey="department_name"
                                    color="#10b981"
                                    height={280}
                                />
                            ) : (
                                <div className="flex items-center justify-center h-full text-muted-foreground text-sm">{t('No salary data')}</div>
                            )}
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-sm">{t('Recent Payroll Records')}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {payroll_analysis.payroll_records.data && payroll_analysis.payroll_records.data.length > 0 ? (
                                <div className="space-y-2">
                                    {payroll_analysis.payroll_records.data.slice(0, 8).map((rec, idx) => (
                                        <div key={idx} className="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                            <div>
                                                <p className="text-sm font-medium">{rec.employee_name}</p>
                                                <p className="text-xs text-muted-foreground">{rec.payroll_title}</p>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-sm font-semibold">${(rec.net_pay || 0).toLocaleString()}</p>
                                                <Badge variant={rec.payroll_status === 'completed' ? 'default' : 'secondary'} className="text-xs">
                                                    {rec.payroll_status}
                                                </Badge>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="text-center py-8 text-muted-foreground text-sm">{t('No payroll records')}</div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* Section 5: Employee ROI */}
            <div className="mb-8">
                <h2 className="text-lg font-semibold mb-4 flex items-center gap-2">
                    <Target className="h-5 w-5 text-indigo-600" />
                    {t('Employee ROI & Revenue Contribution')}
                </h2>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-sm">{t('Employee ROI Analysis')}</CardTitle>
                        <CardDescription>{t('Revenue return per dollar of salary')}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {employee_roi && employee_roi.length > 0 ? (
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="border-b text-left">
                                            <th className="pb-2 font-medium text-muted-foreground">{t('Employee')}</th>
                                            <th className="pb-2 font-medium text-muted-foreground">{t('Department')}</th>
                                            <th className="pb-2 font-medium text-muted-foreground text-right">{t('Salary')}</th>
                                            <th className="pb-2 font-medium text-muted-foreground text-right">{t('Tasks')}</th>
                                            <th className="pb-2 font-medium text-muted-foreground text-right">{t('ROI Ratio')}</th>
                                            <th className="pb-2 font-medium text-muted-foreground">{t('Category')}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {employee_roi.map((emp, idx) => {
                                            const roiCat = emp.roi_ratio >= 3 ? 'high' : emp.roi_ratio >= 1.5 ? 'optimal' : emp.roi_ratio >= 0.8 ? 'under' : 'training';
                                            const roiColor = roiCat === 'high' ? 'text-green-600 bg-green-100' : roiCat === 'optimal' ? 'text-blue-600 bg-blue-100' : roiCat === 'under' ? 'text-yellow-600 bg-yellow-100' : 'text-red-600 bg-red-100';
                                            const roiLabel = roiCat === 'high' ? t('High') : roiCat === 'optimal' ? t('Optimal') : roiCat === 'under' ? t('Under-utilized') : t('Training');
                                            return (
                                                <tr key={idx} className="border-b last:border-0">
                                                    <td className="py-2 font-medium">{emp.employee_name}</td>
                                                    <td className="py-2 text-muted-foreground">{emp.department_name || ''}</td>
                                                    <td className="py-2 text-right">${(emp.monthly_salary || 0).toLocaleString()}</td>
                                                    <td className="py-2 text-right">{emp.tasks_completed}</td>
                                                    <td className="py-2 text-right font-semibold">{emp.roi_ratio}x</td>
                                                    <td className="py-2">
                                                        <span className={`text-xs px-2 py-1 rounded-full ${roiColor}`}>{roiLabel}</span>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>
                        ) : (
                            <div className="text-center py-8 text-muted-foreground text-sm">{t('No ROI data available')}</div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}