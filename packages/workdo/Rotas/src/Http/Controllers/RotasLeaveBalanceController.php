<?php

namespace Workdo\Rotas\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use Workdo\Rotas\Models\LeaveType;
use Workdo\Rotas\Models\LeaveApplication;
use Workdo\Rotas\Models\Employee;

class RotasLeaveBalanceController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-rotas-leave-balance')) {
            $currentYear = date('Y');

            // Get employees with their leave balances
            $employees = User::whereIn('id', Employee::where('created_by', creatorId())->pluck('user_id'))
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-rotas-leave-balance')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-rotas-leave-balance')) {
                        $q->where('id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), function($q) {
                    $q->where('name', 'like', '%' . request('search') . '%');
                })
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->orderBy('name'))
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $leaveTypes = LeaveType::where('created_by', creatorId())->get();

            // Transform paginated employees to include leave balance data
            $employees->getCollection()->transform(function ($employee) use ($leaveTypes, $currentYear) {
                $leaveTypesData = [];
                
                foreach ($leaveTypes as $leaveType) {
                    $usedLeaves = LeaveApplication::where('employee_id', $employee->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->where('status', 'approved')
                        ->whereYear('start_date', $currentYear)
                        ->sum('total_days');

                    $leaveTypesData[] = [
                        'leave_type_name' => $leaveType->name,
                        'leave_type_color' => $leaveType->color,
                        'total_days' => $leaveType->max_days_per_year,
                        'used_days' => $usedLeaves,
                        'available_days' => $leaveType->max_days_per_year - $usedLeaves,
                    ];
                }
                
                return [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'leave_types' => $leaveTypesData,
                ];
            });

            return Inertia::render('Rotas/LeaveBalance/Index', [
                'leaveBalances' => $employees,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}
