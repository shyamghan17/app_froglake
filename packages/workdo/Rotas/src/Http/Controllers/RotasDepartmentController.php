<?php

namespace Workdo\Rotas\Http\Controllers;

use Workdo\Rotas\Models\Department;
use Workdo\Rotas\Models\Branch;
use Workdo\Rotas\Http\Requests\StoreRotasDepartmentRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasDepartmentRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Rotas\Events\CreateDepartment;
use Workdo\Rotas\Events\UpdateDepartment;
use Workdo\Rotas\Events\DestroyDepartment;

class RotasDepartmentController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-rotas-departments')){
            $departments = Department::with('branch')->select('id', 'department_name', 'branch_id', 'created_at')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-departments')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-departments')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();

            return Inertia::render('Rotas/SystemSetup/Departments/Index', [
                'departments' => $departments,
                'branches' => Branch::where('created_by', creatorId())->select('id', 'branch_name')->get(),
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasDepartmentRequest $request)
    {
        if(Auth::user()->can('create-rotas-departments')){
            $validated = $request->validated();

            $department = new Department();
            $department->department_name = $validated['department_name'];
            $department->branch_id = $validated['branch_id'];

            $department->creator_id = Auth::id();
            $department->created_by = creatorId();
            $department->save();

            // Dispatch event for packages to handle their fields
            CreateDepartment::dispatch($request, $department);
            return back()->with('success', __('The department has been created successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasDepartmentRequest $request, Department $department)
    {
        if(Auth::user()->can('edit-rotas-departments')){
            $validated = $request->validated();

            $department->department_name = $validated['department_name'];
            $department->branch_id = $validated['branch_id'];

            $department->save();

            // Dispatch event for packages to handle their fields
            UpdateDepartment::dispatch($request, $department);
            return back()->with('success', __('The department details are updated successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(Department $department)
    {
        if(Auth::user()->can('delete-rotas-departments')){
            // Dispatch event for packages to handle their fields
            DestroyDepartment::dispatch($department);
            $department->delete();

            return back()->with('success', __('The department has been deleted.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }


}