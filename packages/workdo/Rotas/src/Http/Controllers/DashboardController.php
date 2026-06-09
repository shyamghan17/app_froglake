<?php

namespace Workdo\Rotas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Rotas\Models\Employee;
use Workdo\Rotas\Models\Rota;
use Workdo\Rotas\Models\LeaveApplication;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if(Auth::user()->can('manage-rotas')){
            $settings = getCompanyAllSetting();
            
            // Get employees based on permissions and settings
            $employeesQuery = Employee::with(['user', 'rotas' => function($q) {
                $q->whereBetween('rotas_date', [now()->startOfMonth(), now()->endOfMonth()]);
            }])->where(function($q) use ($settings) {
                if(Auth::user()->can('manage-any-employees')) {
                    $q->where('created_by', creatorId());
                } elseif($settings['rotas_employees_see_only_themselves'] === '1' && Auth::user()->not_emp_type) {
                    $q->where('user_id', Auth::id());
                } elseif(Auth::user()->can('manage-own-employees')) {
                    $q->where('creator_id', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            });
            
            $employees = $employeesQuery->get();
            
            // Get rotas based on same permissions
            $rotasQuery = Rota::where(function($q) use ($settings) {
                if(Auth::user()->can('manage-any-rotas')) {
                    $q->where('created_by', creatorId());
                } elseif($settings['rotas_employees_see_only_themselves'] === '1' && Auth::user()->not_emp_type) {
                    $q->where('user_id', Auth::id());
                } elseif(Auth::user()->can('manage-own-rotas')) {
                    $q->where('issued_by', Auth::id());
                } else {
                    $q->whereRaw('1 = 0');
                }
            });
            
            $totalRotas = $rotasQuery->clone()->count();
            $publishedRotas = $rotasQuery->clone()->where('is_published', true)->count();
            $pendingRotas = $rotasQuery->clone()->where('is_published', false)->count();
            
            // Get calendar shifts for current month with publish filter
            $showUnpublishedRotas = (isset($settings['rotas_include_unpublished_shifts']) && ($settings['rotas_include_unpublished_shifts'] === '1' || $settings['rotas_include_unpublished_shifts'] === true));
            
            $calendarShifts = $rotasQuery->clone()
                ->with(['user', 'branch', 'department', 'designation', 'shift', 'issuedBy'])
                ->when(!$showUnpublishedRotas, function($q) {
                    $q->where('is_published', true);
                })
                ->whereIn('type', ['shift', 'leave'])
                ->orderBy('rotas_date', 'asc')
                ->get()
                ->map(function($rota) {
                    return [
                        'id' => $rota->id,
                        'employee_name' => $rota->user ? $rota->user->name : 'Unknown Employee',
                        'date' => $rota->rotas_date ? $rota->rotas_date->format('Y-m-d') : now()->format('Y-m-d'),
                        'start_time' => $rota->start_time ? $rota->start_time->format('Y-m-d H:i') : '',
                        'end_time' => $rota->end_time ? $rota->end_time->format('Y-m-d H:i') : '',
                        'type' => $rota->type ?? 'shift',
                        'is_published' => $rota->is_published,
                        'branch_name' => $rota->branch ? $rota->branch->branch_name : null,
                        'department_name' => $rota->department ? $rota->department->department_name : null,
                        'designation_name' => $rota->designation ? $rota->designation->designation_name : null,
                        'shift_name' => $rota->shift ? $rota->shift->shift_name : null,
                        'break_time' => $rota->break_time,
                        'notes' => $rota->notes,
                        'issued_by_name' => $rota->issuedBy ? $rota->issuedBy->name : null,
                        'total_hours' => $rota->total_working_hours,
                    ];
                });


            // Get approved leave applications for current month
            $leaveApplications = collect([]);
            if (module_is_active('Hrm')) {
                $leaveApplications = LeaveApplication::where('status', 'approved')
                    ->where(function ($query) {
                        $query->whereBetween('start_date', [now()->startOfMonth(), now()->endOfMonth()])
                            ->orWhereBetween('end_date', [now()->startOfMonth(), now()->endOfMonth()])
                            ->orWhere(function ($q) {
                                $q->where('start_date', '<=', now()->startOfMonth())
                                    ->where('end_date', '>=', now()->endOfMonth());
                            });
                    })
                    ->with(['leave_type', 'employee'])
                    ->get()
                    ->map(function($leave) use ($employees) {
                        $employee = $employees->firstWhere('user_id', $leave->employee_id);
                        if (!$employee) return null;
                        
                        $leaveEvents = [];
                        $startDate = $leave->start_date;
                        $endDate = $leave->end_date;
                        $currentDate = $startDate->copy();
                        
                        while ($currentDate <= $endDate) {
                            $leaveEvents[] = [
                                'id' => 'leave-' . $leave->id . '-' . $currentDate->format('Y-m-d'),
                                'employee_name' => $employee->user ? $employee->user->name : 'Unknown Employee',
                                'date' => $currentDate->format('Y-m-d'),
                                'start_time' => '',
                                'end_time' => '',
                                'type' => 'leave',
                                'is_published' => true,
                                'leave_type' => $leave->leave_type->name ?? 'Leave',
                                'reason' => $leave->reason,
                            ];
                            $currentDate->addDay();
                        }
                        
                        return $leaveEvents;
                    })
                    ->filter()
                    ->flatten(1);
            }

            // Merge rotas and leaves
            $calendarShifts = $calendarShifts->concat($leaveApplications)->sortBy('date')->values();

            return Inertia::render('Rotas/Index', [
                'stats' => [
                    'total_employees' => $employees->count(),
                    'total_rotas' => $totalRotas,
                    'published_rotas' => $publishedRotas,
                    'pending_rotas' => $pendingRotas,
                ],
                'calendarShifts' => $calendarShifts,
                'message' => __('Rotas Dashboard - Manage your employee schedules efficiently.')
            ]);
        }
        return back()->with('error', __('Permission denied'));
    }
}