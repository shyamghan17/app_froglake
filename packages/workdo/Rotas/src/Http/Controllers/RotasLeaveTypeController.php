<?php

namespace Workdo\Rotas\Http\Controllers;

use Workdo\Rotas\Models\LeaveType;
use Workdo\Rotas\Http\Requests\StoreRotasLeaveTypeRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasLeaveTypeRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Rotas\Events\CreateLeaveType;
use Workdo\Rotas\Events\UpdateLeaveType;
use Workdo\Rotas\Events\DestroyLeaveType;

class RotasLeaveTypeController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-rotas-leave-types')){
            $leaveTypes = LeaveType::query()
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-leave-types')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-leave-types')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('search'), function($q) {
                    $q->where(function($query) {
                        $query->where('name', 'like', '%' . request('search') . '%')
                              ->orWhere('description', 'like', '%' . request('search') . '%');
                    });
                })
                
                ->when(request('is_paid') !== null, function($q) {
                    $q->where('is_paid', request('is_paid'));
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('Rotas/LeaveTypes/Index', [
                'leaveTypes' => $leaveTypes,
            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasLeaveTypeRequest $request)
    {
        if(Auth::user()->can('create-rotas-leave-types')){
            $validated = $request->validated();

            $validated['is_paid'] = $request->boolean('is_paid', false);

            $leaveType = new LeaveType();
            $leaveType->name = $validated['name'];
            $leaveType->description = $validated['description'];
            $leaveType->max_days_per_year = $validated['max_days_per_year'];
            $leaveType->is_paid = $validated['is_paid'];
            $leaveType->color = $validated['color'];

            $leaveType->creator_id = Auth::id();
            $leaveType->created_by = creatorId();
            $leaveType->save();

            // Dispatch event for packages to handle their fields
            CreateLeaveType::dispatch($request, $leaveType);
            return back()->with('success', __('The leave type has been created successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasLeaveTypeRequest $request, LeaveType $leaveType)
    {
        if(Auth::user()->can('edit-rotas-leave-types')){
            $validated = $request->validated();

            $validated['is_paid'] = $request->boolean('is_paid', false);

            $leaveType->name = $validated['name'];
            $leaveType->description = $validated['description'];
            $leaveType->max_days_per_year = $validated['max_days_per_year'];
            $leaveType->is_paid = $validated['is_paid'];
            $leaveType->color = $validated['color'];

            $leaveType->save();

            // Dispatch event for packages to handle their fields
            UpdateLeaveType::dispatch($request, $leaveType);

            return back()->with('success', __('The leave type details are updated successfully.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(LeaveType $leaveType)
    {
        if(Auth::user()->can('delete-rotas-leave-types')){
            // Dispatch event for packages to handle their fields
            DestroyLeaveType::dispatch($leaveType);
            $leaveType->delete();

            return back()->with('success', __('The leave type has been deleted.'));
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }
}