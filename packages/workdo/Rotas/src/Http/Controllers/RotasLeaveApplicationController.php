<?php

namespace Workdo\Rotas\Http\Controllers;

use Workdo\Rotas\Models\LeaveApplication;
use Workdo\Rotas\Http\Requests\StoreRotasLeaveApplicationRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasLeaveApplicationRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Workdo\Rotas\Models\LeaveType;
use Workdo\Rotas\Models\Employee;
use Workdo\Rotas\Events\CreateLeaveApplication;
use Workdo\Rotas\Events\UpdateLeaveApplication;
use Workdo\Rotas\Events\DestroyLeaveApplication;
use Workdo\Rotas\Events\UpdateLeaveStatus;
use Carbon\Carbon;

class RotasLeaveApplicationController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-rotas-leave-applications')) {
            $leaveapplications = LeaveApplication::query()
                ->with(['employee', 'leave_type', 'approved_by'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-rotas-leave-applications')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-rotas-leave-applications')) {
                        $q->where('creator_id', Auth::id())->orWhere('employee_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('reason'), function ($q) {
                    $q->where(function ($query) {
                        $query
                            ->where('reason', 'like', '%' . request('reason') . '%')
                            ->orWhereHas('employee', function ($subQuery) {
                                $subQuery->where('name', 'like', '%' . request('reason') . '%');
                            })
                            ->orWhereHas('leave_type', function ($subQuery) {
                                $subQuery->where('name', 'like', '%' . request('reason') . '%');
                            });
                    });
                })
                ->when(request('status') !== null && request('status') !== '', fn($q) => $q->where('status', request('status')))
                ->when(request('employee_id'), fn($q) => $q->where('employee_id', request('employee_id')))
                ->when(request('leave_type_id'), fn($q) => $q->where('leave_type_id', request('leave_type_id')))
                ->when(request('start_date'), fn($q) => $q->whereDate('start_date', '>=', request('start_date')))
                ->when(request('end_date'), fn($q) => $q->whereDate('end_date', '<=', request('end_date')))
                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('Rotas/LeaveApplications/Index', [
                'leaveapplications' => $leaveapplications,
                'employees' => $this->getFilteredEmployees(),
                'leavetypes' => LeaveType::where('created_by', creatorId())->select('id', 'name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasLeaveApplicationRequest $request)
    {
        if (Auth::user()->can('create-rotas-leave-applications')) {
            $validated = $request->validated();

            // Calculate total days automatically
            $startDate = new \DateTime($validated['start_date']);
            $endDate = new \DateTime($validated['end_date']);
            $totalDays = $startDate->diff($endDate)->days + 1;

            // Get leave type details
            $leaveType = LeaveType::find($validated['leave_type_id']);
            if (!$leaveType) {
                return redirect()
                    ->back()
                    ->withErrors(['leave_type_id' => __('Invalid leave type selected.')]);
            }

            // Get current year
            $currentYear = date('Y');

            // Calculate used leaves for this employee, leave type and current year
            $usedLeaves = LeaveApplication::where('employee_id', $validated['employee_id'])
                ->where('leave_type_id', $validated['leave_type_id'])
                ->whereIn('status', ['approved', 'pending'])
                ->whereYear('start_date', $currentYear)
                ->sum('total_days');

            // Check for overlapping leave applications
            $overlappingLeave = LeaveApplication::where('employee_id', $validated['employee_id'])
                ->where(function ($query) use ($validated) {
                    $query
                        ->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('start_date', '<=', $validated['start_date'])->where('end_date', '>=', $validated['end_date']);
                        });
                })
                ->whereIn('status', ['approved', 'pending'])
                ->first();

            if ($overlappingLeave) {
                $startDate = Carbon::parse($overlappingLeave->start_date)->format('Y-m-d');
                $endDate = Carbon::parse($overlappingLeave->end_date)->format('Y-m-d');

                return back()->withErrors([
                        'start_date' => "Leave already applied for overlapping dates from {$startDate} to {$endDate}",
                    ]);
            }

            // Check if requested days exceed available balance
            $availableLeaves = $leaveType->max_days_per_year - $usedLeaves;
            if ($totalDays > $availableLeaves) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'start_date' => __('Insufficient leave balance. Available: :available days, Requested: :requested days', [
                            'available' => $availableLeaves,
                            'requested' => $totalDays,
                        ]),
                    ]);
            }

            $leaveapplication = new LeaveApplication();
            $leaveapplication->start_date = $validated['start_date'];
            $leaveapplication->end_date = $validated['end_date'];
            $leaveapplication->total_days = $totalDays;
            $leaveapplication->reason = $validated['reason'];
            $leaveapplication->attachment = $validated['attachment'] ?? null;
            $leaveapplication->status = 'pending';
            $leaveapplication->employee_id = $validated['employee_id'];
            $leaveapplication->leave_type_id = $validated['leave_type_id'];

            $leaveapplication->creator_id = Auth::id();
            $leaveapplication->created_by = creatorId();
            $leaveapplication->save();

            CreateLeaveApplication::dispatch($request, $leaveapplication);

            return back()->with('success', __('The leave application has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasLeaveApplicationRequest $request, LeaveApplication $leaveapplication)
    {
        if (Auth::user()->can('edit-rotas-leave-applications')) {
            $validated = $request->validated();

            // Calculate total days automatically
            $startDate = new \DateTime($validated['start_date']);
            $endDate = new \DateTime($validated['end_date']);
            $totalDays = $startDate->diff($endDate)->days + 1;

            // Get leave type details
            $leaveType = LeaveType::find($validated['leave_type_id']);
            if (!$leaveType) {
                return redirect()
                    ->back()
                    ->withErrors(['leave_type_id' => __('Invalid leave type selected.')]);
            }

            // Get current year
            $currentYear = date('Y');

            // Calculate used leaves for this employee, leave type and current year (excluding current application)
            $usedLeaves = LeaveApplication::where('employee_id', $validated['employee_id'])
                ->where('leave_type_id', $validated['leave_type_id'])
                ->whereIn('status', ['approved', 'pending'])
                ->whereYear('start_date', $currentYear)
                ->where('id', '!=', $leaveapplication->id)
                ->sum('total_days');

            // Check for overlapping leave applications (excluding current application)
            $overlappingLeave = LeaveApplication::where('employee_id', $validated['employee_id'])
                ->where('id', '!=', $leaveapplication->id)
                ->where(function ($query) use ($validated) {
                    $query
                        ->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('start_date', '<=', $validated['start_date'])->where('end_date', '>=', $validated['end_date']);
                        });
                })
                ->whereIn('status', ['approved', 'pending'])
                ->first();

            if ($overlappingLeave) {
                $startDate = Carbon::parse($overlappingLeave->start_date)->format('Y-m-d');
                $endDate = Carbon::parse($overlappingLeave->end_date)->format('Y-m-d');

                return redirect()
                    ->back()
                    ->withErrors([
                        'start_date' => "Leave already applied for overlapping dates from {$startDate} to {$endDate}",
                    ]);
            }

            // Check if requested days exceed available balance
            $availableLeaves = $leaveType->max_days_per_year - $usedLeaves;
            if ($totalDays > $availableLeaves) {
                return redirect()
                    ->back()
                    ->withErrors([
                        'start_date' => __('Insufficient leave balance. Available: :available days, Requested: :requested days', [
                            'available' => $availableLeaves,
                            'requested' => $totalDays,
                        ]),
                    ]);
            }

            $leaveapplication->employee_id = $validated['employee_id'];
            $leaveapplication->leave_type_id = $validated['leave_type_id'];
            $leaveapplication->start_date = $validated['start_date'];
            $leaveapplication->end_date = $validated['end_date'];
            $leaveapplication->total_days = $totalDays;
            $leaveapplication->reason = $validated['reason'];
            $leaveapplication->attachment = $validated['attachment'] ?? null;

            $leaveapplication->save();

            UpdateLeaveApplication::dispatch($request, $leaveapplication);

            return back()->with('success', __('The leave application details are updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(LeaveApplication $leaveapplication)
    {
        if (Auth::user()->can('delete-rotas-leave-applications')) {
            DestroyLeaveApplication::dispatch($leaveapplication);
            $leaveapplication->delete();

            return back()->with('success', __('The leave application has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function updateStatus(Request $request, LeaveApplication $leaveapplication)
    {
        if (Auth::user()->can('manage-rotas-leave-status')) {
            $request->validate([
                'status' => 'required|in:pending,approved,rejected',
                'approver_comment' => 'nullable|string',
            ]);

            $leaveapplication->status = $request->status;
            $leaveapplication->approver_comment = $request->approver_comment;

            if ($request->status === 'approved') {
                $leaveapplication->approved_by = Auth::id();
                $leaveapplication->approved_at = now();
            }

            $leaveapplication->save();
            UpdateLeaveStatus::dispatch($request, $leaveapplication);

            return redirect()->back()->with('success', __('The leave status has been updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function getLeaveBalance($employeeId, $leaveTypeId)
    {
        if (Auth::user()->can('view-rotas-leave-applications')) {
            $leaveType = LeaveType::find($leaveTypeId);
            if (!$leaveType) {
                return response()->json(['error' => 'Invalid leave type'], 404);
            }

            $currentYear = date('Y');
            $baseQuery = LeaveApplication::where('employee_id', $employeeId)->where('leave_type_id', $leaveTypeId)->whereYear('start_date', $currentYear);

            // Exclude current leave application if editing
            if (request('exclude_id')) {
                $baseQuery->where('id', '!=', request('exclude_id'));
            }

            $approvedLeaves = (clone $baseQuery)->where('status', 'approved')->sum('total_days');
            $pendingLeaves = (clone $baseQuery)->where('status', 'pending')->sum('total_days');
            $usedLeaves = $approvedLeaves + $pendingLeaves;
            $availableLeaves = $leaveType->max_days_per_year - $usedLeaves;

            return response()->json([
                'total_leaves' => $leaveType->max_days_per_year,
                'approved_leaves' => $approvedLeaves,
                'pending_leaves' => $pendingLeaves,
                'used_leaves' => $usedLeaves,
                'available_leaves' => $availableLeaves,
            ]);
        } else {
            return response()->json([], 403);
        }
    }

    public function getLeaveTypesByEmployee($employeeId)
    {
        if (Auth::user()->can('view-rotas-leave-types')) {
            $leave_types = LeaveType::where('employee_id', $employeeId)->where('created_by', creatorId())->select('id', 'name')->get();

            return response()->json($leave_types);
        } else {
            return response()->json([], 403);
        }
    }

    private function getFilteredEmployees()
    {
        $employeeQuery = Employee::where('created_by', creatorId());

        if (Auth::user()->can('manage-own-rotas-leave-applications') && !Auth::user()->can('manage-any-rotas-leave-applications')) {
            $employeeQuery->where(function ($q) {
                $q->where('creator_id', Auth::id())->orWhere('user_id', Auth::id());
            });
        }

        return User::emp()->where('created_by', creatorId())
            ->whereIn('id', $employeeQuery->pluck('user_id'))
            ->select('id', 'name')->get();
    }
}
