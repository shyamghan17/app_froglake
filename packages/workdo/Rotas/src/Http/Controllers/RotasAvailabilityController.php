<?php

namespace Workdo\Rotas\Http\Controllers;

use Workdo\Rotas\Models\RotasAvailability;
use Workdo\Rotas\Http\Requests\StoreRotasAvailabilityRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasAvailabilityRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use Workdo\Rotas\Models\Employee;
use Workdo\Rotas\Events\CreateAvailability;
use Workdo\Rotas\Events\UpdateAvailability;
use Workdo\Rotas\Events\DestroyAvailability;
use Carbon\Carbon;

class RotasAvailabilityController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-rotas-availabilities')){
            $availabilities = RotasAvailability::with('employee.user')
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-rotas-availabilities')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-rotas-availabilities')) {
                        $q->where('creator_id', Auth::id())->orWhereHas('employee', function ($empQuery) {
                            $empQuery->where('user_id', Auth::id());
                        });
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), function($q) {
                    $q->where(function($query) {
                        $query->where('name', 'like', '%' . request('search') . '%')
                              ->orWhereHas('employee', function($empQuery) {
                                  $empQuery->where('name', 'like', '%' . request('search') . '%');
                              });
                    });
                })
                ->when(request('employee_id'), function($q) {
                    $q->where('employee_id', request('employee_id'));
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            $employees = Employee::where('created_by', creatorId())
                ->with(['user:id,name', 'shifts'])
                ->get()
                ->map(function($employee) {
                    return [
                        'id' => $employee->id,
                        'name' => $employee->user->name ?? 'Unknown',
                        'shift' => $employee->shifts ? [
                            'id' => $employee->shifts->id,
                            'shift_name' => $employee->shifts->shift_name,
                            'start_time' => Carbon::parse($employee->shifts->start_time)->format('H:i:s'),
                            'end_time' => Carbon::parse($employee->shifts->end_time)->format('H:i:s')
                        ] : null
                    ];
                });

            $currentUserEmployee = $startTime = $endTime = null;
            $isNotEmpType = in_array(Auth::user()->type, Auth::user()->not_emp_type);
            if (!$isNotEmpType) {
                $currentUserEmployee = Employee::with('shifts')->where('user_id', Auth::id())
                    ->where('created_by', creatorId())
                    ->first();
                
                if ($currentUserEmployee && $currentUserEmployee->shifts) {
                    $startTime = Carbon::parse($currentUserEmployee->shifts->start_time)->format('H:i:s');
                    $endTime = Carbon::parse($currentUserEmployee->shifts->end_time)->format('H:i:s');
                }
            }

            return Inertia::render('Rotas/Availabilities/Index', [
                'availabilities' => $availabilities,
                'employees' => $employees,
                'currentUserEmployee' => $currentUserEmployee,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'isNotEmpType' => $isNotEmpType,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasAvailabilityRequest $request)
    {
        if(Auth::user()->can('create-rotas-availabilities')){
            $validated = $request->validated();

            $availability = new RotasAvailability();
            $availability->employee_id = $validated['employee_id'];
            $availability->name = $validated['name'];
            $availability->start_date = $validated['start_date'];
            $availability->end_date = $validated['end_date'];
            $availability->availability = $validated['availability'];
            $availability->creator_id = Auth::id();
            $availability->created_by = creatorId();
            $availability->save();

            CreateAvailability::dispatch($request, $availability);
            return back()->with('success', __('The availability has been created successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasAvailabilityRequest $request, RotasAvailability $availability)
    {
        if (Auth::user()->can('edit-rotas-availabilities')) {
            $validated = $request->validated();

            $availability->employee_id = $validated['employee_id'];
            $availability->name = $validated['name'];
            $availability->start_date = $validated['start_date'];
            $availability->end_date = $validated['end_date'];
            $availability->availability = $validated['availability'];
            $availability->save();

            UpdateAvailability::dispatch($request, $availability);
            return back()->with('success', __('The availability has been updated successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(RotasAvailability $availability)
    {
        if(Auth::user()->can('delete-rotas-availabilities')){
            DestroyAvailability::dispatch($availability);
            $availability->delete();

            return back()->with('success', __('The availability has been deleted.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }
}