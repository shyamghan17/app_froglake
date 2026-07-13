<?php

namespace Workdo\SmartDashboardAnalytics\Services;

use Illuminate\Support\Facades\DB;

class TeamPerformanceService
{
    public function getTeamData()
    {
        return [
            'employee_overview' => $this->getEmployeeOverview(),
            'attendance_analytics' => $this->getAttendanceAnalytics(),
            'department_comparison' => $this->getDepartmentComparison(),
            'payroll_analysis' => $this->getPayrollAnalysis(),
            'employee_roi' => $this->getEmployeeRoi(),
        ];
    }

    private function getEmployeeOverview()
    {
        $createdBy = creatorId();

        $totalEmployees = DB::table('employees')
            ->where('created_by', $createdBy)
            ->where('user_id', '!=', null)
            ->count();

        $presentToday = DB::table('attendances')
            ->whereDate('date', now()->today())
            ->where('status', 'present')
            ->count();

        $totalPayroll = DB::table('employees')
            ->where('created_by', $createdBy)
            ->where('user_id', '!=', null)
            ->sum('basic_salary');

        $departmentDistribution = DB::table('employees as e')
            ->join('departments as d', 'e.department_id', '=', 'd.id')
            ->where('e.created_by', $createdBy)
            ->where('e.user_id', '!=', null)
            ->selectRaw('d.department_name, COUNT(e.id) as employee_count')
            ->groupBy('d.id', 'd.department_name')
            ->orderBy('employee_count', 'desc')
            ->get();

        $employeeList = DB::table('employees as e')
            ->join('users as u', 'e.user_id', '=', 'u.id')
            ->leftJoin('departments as d', 'e.department_id', '=', 'd.id')
            ->leftJoin('designations as des', 'e.designation_id', '=', 'des.id')
            ->where('e.created_by', $createdBy)
            ->where('e.user_id', '!=', null)
            ->select(
                'u.name as employee_name',
                'e.employee_id',
                'd.department_name',
                'des.designation_name',
                DB::raw('(SELECT ROUND((COUNT(CASE WHEN status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) FROM attendances WHERE employee_id = e.user_id AND MONTH(date) = MONTH(CURDATE())) as attendance_percentage'),
                DB::raw('(SELECT COUNT(*) FROM project_tasks WHERE JSON_CONTAINS(project_tasks.assigned_to, CAST(e.user_id AS JSON)) AND MONTH(updated_at) = MONTH(CURDATE())) as tasks_completed'),
                DB::raw('(SELECT COALESCE(SUM(total_hour), 0) FROM attendances WHERE employee_id = e.user_id AND MONTH(date) = MONTH(CURDATE())) as hours_worked'),
                'e.basic_salary'
            )
            ->orderBy('attendance_percentage', 'desc')
            ->paginate(50);

        return [
            'kpi' => [
                'total_employees' => $totalEmployees,
                'present_today' => $presentToday,
                'total_payroll_cost' => $totalPayroll,
            ],
            'department_distribution' => $departmentDistribution,
            'employee_list' => $employeeList,
        ];
    }

    private function getAttendanceAnalytics()
    {
        $createdBy = creatorId();

        $overallAttendance = DB::table('attendances')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->selectRaw('
                ROUND((COUNT(CASE WHEN status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) as overall_percentage
            ')
            ->first()->overall_percentage ?? 0;

        $calendarHeatmap = DB::table('attendances')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->selectRaw('
                DATE(date) as attendance_date,
                COUNT(CASE WHEN status = "present" THEN 1 END) as present_count,
                COUNT(CASE WHEN status = "absent" THEN 1 END) as absent_count,
                COUNT(CASE WHEN status = "half day" THEN 1 END) as half_day_count,
                COUNT(*) as total_employees,
                ROUND((COUNT(CASE WHEN status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) as attendance_rate
            ')
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('attendance_date')
            ->get();

        $attendanceTrend = DB::table('attendances')
            ->where('date', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month, ROUND((COUNT(CASE WHEN status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) as attendance_rate')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $attendanceRecords = DB::table('attendances as a')
            ->join('users as u', 'a.employee_id', '=', 'u.id')
            ->join('employees as e', 'e.user_id', '=', 'u.id')
            ->leftJoin('departments as d', 'e.department_id', '=', 'd.id')
            ->join('shifts as s', 'a.shift_id', '=', 's.id')
            ->select(
                'a.date', 'u.name as employee_name', 'd.department_name', 's.shift_name',
                'a.clock_in', 'a.clock_out', 'a.total_hour', 'a.break_hour',
                'a.overtime_hours', 'a.status',
                DB::raw("CASE WHEN TIME(a.clock_in) > s.start_time THEN 'Late' WHEN a.clock_out IS NOT NULL AND TIME(a.clock_out) < s.end_time THEN 'Early' ELSE 'On Time' END as punctuality")
            )
            ->whereMonth('a.date', now()->month)
            ->orderBy('a.date', 'desc')
            ->paginate(50);

        $leaveRequests = DB::table('leave_applications as la')
            ->join('users as u', 'la.employee_id', '=', 'u.id')
            ->join('employees as e', 'e.user_id', '=', 'u.id')
            ->leftJoin('departments as d', 'e.department_id', '=', 'd.id')
            ->join('leave_types as lt', 'la.leave_type_id', '=', 'lt.id')
            ->where('la.created_by', $createdBy)
            ->select(
                'la.id', 'u.name as employee_name', 'd.department_name', 'lt.name as leave_type_name',
                'la.start_date', 'la.end_date', 'la.total_days', 'la.status', 'la.reason', 'la.created_at as applied_date'
            )
            ->orderBy(DB::raw("CASE WHEN la.status = 'pending' THEN 1 WHEN la.status = 'approved' THEN 2 ELSE 3 END"))
            ->orderBy('la.created_at', 'desc')
            ->get();

        return [
            'kpi' => [
                'overall_attendance' => $overallAttendance,
                'pending_leaves' => DB::table('leave_applications')->where('created_by', $createdBy)->where('status', 'pending')->count(),
            ],
            'calendar_heatmap' => $calendarHeatmap,
            'attendance_trend' => $attendanceTrend,
            'attendance_records' => $attendanceRecords,
            'leave_requests' => $leaveRequests,
        ];
    }

    private function getDepartmentComparison()
    {
        $createdBy = creatorId();

        return DB::table('departments as d')
            ->leftJoin('employees as e', 'd.id', '=', 'e.department_id')
            ->where('e.created_by', $createdBy)
            ->where('e.user_id', '!=', null)
            ->select(
                'd.department_name',
                DB::raw('COUNT(DISTINCT e.id) as employee_count'),
                DB::raw('COALESCE(SUM(e.basic_salary), 0) as total_salary'),
                DB::raw('(SELECT COUNT(*) FROM project_tasks pt JOIN employees emp ON JSON_CONTAINS(pt.assigned_to, CAST(emp.user_id AS JSON)) WHERE emp.department_id = d.id AND MONTH(pt.updated_at) = MONTH(CURDATE())) as tasks_completed'),
                DB::raw('(SELECT ROUND((COUNT(CASE WHEN a.status = "present" THEN 1 END) / NULLIF(COUNT(*), 0)) * 100, 2) FROM attendances a JOIN employees emp ON a.employee_id = emp.user_id WHERE emp.department_id = d.id AND MONTH(a.date) = MONTH(CURDATE())) as attendance_rate')
            )
            ->groupBy('d.id', 'd.department_name')
            ->orderBy('employee_count', 'desc')
            ->get();
    }

    private function getPayrollAnalysis()
    {
        $createdBy = creatorId();

        $totalPayroll = DB::table('employees')
            ->where('created_by', $createdBy)
            ->where('user_id', '!=', null)
            ->sum('basic_salary');

        $avgSalary = DB::table('employees')
            ->where('created_by', $createdBy)
            ->where('user_id', '!=', null)
            ->avg('basic_salary');

        $salaryByDepartment = DB::table('payrolls as p')
            ->join('payroll_entries as pe', 'p.id', '=', 'pe.payroll_id')
            ->join('employees as e', 'pe.employee_id', '=', 'e.user_id')
            ->join('departments as d', 'e.department_id', '=', 'd.id')
            ->where('p.created_by', $createdBy)
            ->whereMonth('p.pay_date', now()->month)
            ->where('p.status', 'completed')
            ->selectRaw('d.department_name, SUM(pe.gross_pay) as total_gross_pay, SUM(pe.total_deductions) as total_deductions, SUM(pe.net_pay) as total_net_pay, COUNT(pe.id) as employee_count, AVG(pe.net_pay) as avg_net_pay')
            ->groupBy('d.id', 'd.department_name')
            ->orderBy('total_net_pay', 'desc')
            ->get();

        $payrollRecords = DB::table('payrolls as p')
            ->join('payroll_entries as pe', 'p.id', '=', 'pe.payroll_id')
            ->join('employees as e', 'pe.employee_id', '=', 'e.user_id')
            ->join('users as u', 'e.user_id', '=', 'u.id')
            ->leftJoin('departments as d', 'e.department_id', '=', 'd.id')
            ->where('p.created_by', $createdBy)
            ->select(
                'p.title as payroll_title', 'p.payroll_frequency', 'p.pay_period_start', 'p.pay_period_end',
                'p.pay_date', 'p.status as payroll_status', 'u.name as employee_name', 'd.department_name',
                'pe.basic_salary', 'pe.total_allowances', 'pe.total_deductions', 'pe.gross_pay', 'pe.net_pay',
                'pe.working_days', 'pe.present_days', 'pe.absent_days'
            )
            ->orderBy('p.pay_date', 'desc')
            ->paginate(50);

        return [
            'kpi' => [
                'total_payroll' => $totalPayroll,
                'average_salary' => $avgSalary,
            ],
            'salary_by_department' => $salaryByDepartment,
            'payroll_records' => $payrollRecords,
        ];
    }

    private function getEmployeeRoi()
    {
        $createdBy = creatorId();

        return DB::table('employees as e')
            ->join('users as u', 'e.user_id', '=', 'u.id')
            ->leftJoin('departments as d', 'e.department_id', '=', 'd.id')
            ->where('e.created_by', $createdBy)
            ->where('e.user_id', '!=', null)
            ->select(
                'u.name as employee_name',
                'd.department_name',
                'e.basic_salary as monthly_salary',
                DB::raw('(SELECT COUNT(*) FROM project_tasks WHERE JSON_CONTAINS(project_tasks.assigned_to, CAST(e.user_id AS JSON)) AND MONTH(updated_at) = MONTH(CURDATE())) as tasks_completed'),
                DB::raw('COALESCE(e.basic_salary, 0) as payroll_cost'),
                DB::raw('ROUND(COALESCE((SELECT SUM(total_credit) FROM journal_entries WHERE created_by = ' . $createdBy . ' AND MONTH(journal_date) = MONTH(CURDATE())) / NULLIF(e.basic_salary, 0), 0), 2) as roi_ratio')
            )
            ->orderBy('roi_ratio', 'desc')
            ->get();
    }
}