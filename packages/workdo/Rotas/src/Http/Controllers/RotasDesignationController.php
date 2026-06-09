<?php

namespace Workdo\Rotas\Http\Controllers;

use Workdo\Rotas\Models\Designation;
use Workdo\Rotas\Models\Department;
use Workdo\Rotas\Models\Branch;
use Workdo\Rotas\Http\Requests\StoreRotasDesignationRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasDesignationRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Rotas\Events\CreateDesignation;
use Workdo\Rotas\Events\UpdateDesignation;
use Workdo\Rotas\Events\DestroyDesignation;

class RotasDesignationController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-rotas-designations')){
            $designations = Designation::with(['branch', 'department'])->select('id', 'designation_name', 'branch_id', 'department_id', 'created_at')
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-designations')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-designations')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();

            return Inertia::render('Rotas/SystemSetup/Designations/Index', [
                'designations' => $designations,
                'branches' => Branch::where('created_by', creatorId())->select('id', 'branch_name')->get(),
                'departments' => Department::where('created_by', creatorId())->select('id', 'department_name', 'branch_id')->get(),
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasDesignationRequest $request)
    {
        if(Auth::user()->can('create-rotas-designations')){
            $validated = $request->validated();

            $designation = new Designation();
            $designation->designation_name = $validated['designation_name'];
            $designation->branch_id = $validated['branch_id'];
            $designation->department_id = $validated['department_id'];

            $designation->creator_id = Auth::id();
            $designation->created_by = creatorId();
            $designation->save();

            // Dispatch event for packages to handle their fields
            CreateDesignation::dispatch($request, $designation);
            return back()->with('success', __('The designation has been created successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasDesignationRequest $request, Designation $designation)
    {
        if(Auth::user()->can('edit-rotas-designations')){
            $validated = $request->validated();

            $designation->designation_name = $validated['designation_name'];
            $designation->branch_id = $validated['branch_id'];
            $designation->department_id = $validated['department_id'];

            $designation->save();

            // Dispatch event for packages to handle their fields
            UpdateDesignation::dispatch($request, $designation);
            return back()->with('success', __('The designation details are updated successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(Designation $designation)
    {
        if(Auth::user()->can('delete-rotas-designations')){
            // Dispatch event for packages to handle their fields
            DestroyDesignation::dispatch($designation);
            $designation->delete();

            return back()->with('success', __('The designation has been deleted.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }


}