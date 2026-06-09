<?php

namespace Workdo\Rotas\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Rotas\Models\Employee;

class RotasWorkScheduleController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-rotas-work-schedules')) {
            $employees = Employee::query()->with('user:id,name')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-rotas-work-schedules')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-rotas-work-schedules')) {
                        $q->where('user_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->get()
                ->map(function ($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->user->name ?? 'Unknown',
                        'work_schedule' => $employee->work_schedule
                    ];
                });

            return inertia('Rotas/WorkSchedule/Index', [
                'employees' => $employees,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(Request $request, Employee $employee)
    {
        if (Auth::user()->can('edit-rotas-work-schedules')) {
            $validated = $request->validate([
                'work_schedule' => 'required|array',
            ]);

            $employee->work_schedule = $validated['work_schedule'];
            $employee->save();

            return back()->with('success', __('The work schedule has been updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}
